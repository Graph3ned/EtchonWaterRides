<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Rental;
use App\Models\RideType;
use App\Models\Classification;
use App\Models\Ride;
use DateTimeImmutable;
use Exception;

class ImportLegacySqlCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * Usage: php artisan legacy:import-sql path/to/file.sql --dry-run --limit=100
     *
     * @var string
     */
    protected $signature = 'legacy:import-sql {path : Path to the SQL dump} {--dry-run : Parse and report without writing} {--limit= : Limit number of rows per table} {--seed-prices : Seed ride types/classifications/rides from legacy prices table before rentals}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import users and rentals from legacy MySQL dump (users, rides_rental_dbs -> rentals).';

    public function handle(): int
    {
        $path = (string) $this->argument('path');
        $dryRun = (bool) $this->option('dry-run');
        $limitOpt = $this->option('limit');
        $limit = is_null($limitOpt) ? null : (int) $limitOpt;

        if (!is_readable($path)) {
            $this->error("File not readable: {$path}");
            return self::FAILURE;
        }

        $sql = file_get_contents($path);
        if ($sql === false) {
            $this->error('Failed to read file.');
            return self::FAILURE;
        }

        $usersTuples = $this->extractInsertTuples($sql, 'users');
        $rentalsTuples = $this->extractInsertTuples($sql, 'rides_rental_dbs');
        $pricesTuples = $this->option('seed-prices') ? $this->extractInsertTuples($sql, 'prices') : [];

        $this->info('Parsed tuples: users=' . count($usersTuples) . ', rides_rental_dbs=' . count($rentalsTuples) . ', prices=' . count($pricesTuples));
        if (!is_null($limit)) {
            $usersTuples = array_slice($usersTuples, 0, $limit);
            $rentalsTuples = array_slice($rentalsTuples, 0, $limit);
            if (!empty($pricesTuples)) { $pricesTuples = array_slice($pricesTuples, 0, $limit); }
        }

        if ($dryRun) {
            $this->line('Dry run: showing first 3 tuples for each table.');
            $this->previewTuples('users', $usersTuples);
            if (!empty($pricesTuples)) { $this->previewTuples('prices', $pricesTuples); }
            $this->previewTuples('rides_rental_dbs', $rentalsTuples);
            return self::SUCCESS;
        }

        DB::beginTransaction();
        try {
            if (!empty($pricesTuples)) {
                $seeded = $this->seedFromPrices($pricesTuples);
                $this->info("Seeded from prices: ride_types={$seeded['ride_types']}, classifications={$seeded['classifications']}, rides={$seeded['rides']}");
            }
            $usersImported = $this->importUsers($usersTuples);
            $rentalsImported = $this->importRentals($rentalsTuples);
            DB::commit();
            $this->info("Imported users={$usersImported}, rentals={$rentalsImported}");
        } catch (Exception $e) {
            DB::rollBack();
            $this->error('Import failed: ' . $e->getMessage());
            return self::FAILURE;
        }

        return self::SUCCESS;
    }

    /**
     * Extract array of value-tuples from an INSERT INTO `table` VALUES (...),(...);
     * Returns each tuple as array of raw string values (without outer quotes for quoted strings).
     */
    private function extractInsertTuples(string $sql, string $table): array
    {
        $result = [];
        $pattern = '/INSERT\s+INTO\s+`' . preg_quote($table, '/') . '`\s+VALUES\s*(.+?);/is';
        if (!preg_match_all($pattern, $sql, $matches)) {
            return $result;
        }
        foreach ($matches[1] as $valuesBlob) {
            $cursor = 0;
            $length = strlen($valuesBlob);
            while ($cursor < $length) {
                // Skip whitespace and commas
                while ($cursor < $length && (ctype_space($valuesBlob[$cursor]) || $valuesBlob[$cursor] === ',')) {
                    $cursor++;
                }
                if ($cursor >= $length) {
                    break;
                }
                if ($valuesBlob[$cursor] !== '(') {
                    // Unexpected; advance to next
                    $cursor++;
                    continue;
                }
                // Parse tuple
                [$tuple, $nextPos] = $this->parseTuple($valuesBlob, $cursor);
                if (!empty($tuple)) {
                    $result[] = $tuple;
                }
                $cursor = $nextPos;
            }
        }
        return $result;
    }

    /**
     * Parse a single parenthesized tuple, starting at '('; returns [values[], nextPos]
     */
    private function parseTuple(string $s, int $pos): array
    {
        $values = [];
        $i = $pos;
        $len = strlen($s);
        if ($s[$i] !== '(') {
            return [[], $i + 1];
        }
        $i++; // after '('
        $current = '';
        $inString = false;
        $quote = '';
        $parenDepth = 1;
        while ($i < $len && $parenDepth > 0) {
            $ch = $s[$i];
            if ($inString) {
                if ($ch === '\\' && $i + 1 < $len) {
                    $current .= $s[$i + 1];
                    $i += 2;
                    continue;
                }
                if ($ch === $quote) {
                    $inString = false;
                    $i++;
                    continue;
                }
                $current .= $ch;
                $i++;
                continue;
            }
            if ($ch === '\'' || $ch === '"') {
                $inString = true;
                $quote = $ch;
                $i++;
                continue;
            }
            if ($ch === '(') {
                // Should not happen in simple VALUES; but guard
                $parenDepth++;
                $current .= $ch;
                $i++;
                continue;
            }
            if ($ch === ')') {
                $parenDepth--;
                if ($parenDepth === 0) {
                    $values[] = $this->finalizeScalar($current);
                    $i++;
                    break;
                }
                $current .= $ch;
                $i++;
                continue;
            }
            if ($ch === ',') {
                $values[] = $this->finalizeScalar($current);
                $current = '';
                $i++;
                continue;
            }
            $current .= $ch;
            $i++;
        }
        // Advance to next comma or end
        while ($i < $len && $s[$i] !== '(') {
            if ($s[$i] === ',') { $i++; break; }
            $i++;
        }
        return [$values, $i];
    }

    private function finalizeScalar(string $raw)
    {
        $trim = trim($raw);
        if ($trim === 'NULL' || $trim === 'null' || $trim === '') {
            return null;
        }
        // Numeric
        if (is_numeric($trim)) {
            // Preserve decimal strings
            return $trim + 0; // cast to int/float
        }
        return $trim;
    }

    private function previewTuples(string $table, array $tuples): void
    {
        $sample = array_slice($tuples, 0, 3);
        foreach ($sample as $idx => $t) {
            $this->line("{$table}[{$idx}]: " . json_encode($t));
        }
    }

    private function importUsers(array $tuples): int
    {
        // Legacy users columns:
        // (id, name, email, userType, email_verified_at, password, remember_token, created_at, updated_at)
        $count = 0;
        foreach ($tuples as $t) {
            if (count($t) < 9) { continue; }
            [$legacyId, $name, $email, $userType, $emailVerifiedAt, $passwordHash, $rememberToken, $createdAt, $updatedAt] = $t;

            $username = $this->generateUsername($name, $email);

            // Upsert by email; if duplicate, append counter to username
            $user = User::withTrashed()->where('email', (string) $email)->first();
            if (!$user) {
                $user = new User();
            }

            $user->name = (string) $name;
            $user->email = (string) $email;
            $user->username = $this->uniqueUsername($username, $user->exists ? $user->id : null);
            $user->userType = (string) $userType;
            $user->password = (string) $passwordHash; // already hashed
            $user->remember_token = $rememberToken ? (string) $rememberToken : null;
            $user->created_at = $createdAt ? (string) $createdAt : now();
            $user->updated_at = $updatedAt ? (string) $updatedAt : now();

            $user->save();
            $count++;
        }
        return $count;
    }

    /**
     * Seed RideType, Classification, and placeholder Ride records from legacy prices rows.
     * prices tuple columns observed: (id, rideTypeName, classificationName, price_per_hour, created_at, updated_at)
     */
    private function seedFromPrices(array $tuples): array
    {
        $rtCount = 0; $cCount = 0; $rCount = 0;
        foreach ($tuples as $t) {
            if (count($t) < 6) { continue; }
            [$id, $rideTypeName, $classificationName, $pricePerHour, $createdAt, $updatedAt] = $t;

            // Map legacy Clear_Kayak -> Clear Kayak / Double with identifiers
            if ($this->isLegacyClearKayak((string) $rideTypeName)) {
                $rideType = $this->ensureRideType('Clear Kayak');
                if ($rideType->wasRecentlyCreated ?? false) { $rtCount++; }

                $classification = Classification::withTrashed()
                    ->where('ride_type_id', $rideType->id)
                    ->where('name', 'Double')
                    ->first();
                if (!$classification) {
                    $classification = new Classification();
                    $classification->ride_type_id = $rideType->id;
                    $classification->name = 'Double';
                    $classification->price_per_hour = is_numeric($pricePerHour) ? (float) $pricePerHour : 0.0;
                    $classification->created_at = $createdAt ?: now();
                    $classification->updated_at = $updatedAt ?: now();
                    $classification->save();
                    $cCount++;
                }

                // Ensure both target rides exist under Double
                $r1 = $this->ensureRideByIdentifier($classification, 'Black Paddle', $createdAt, $updatedAt);
                if ($r1->wasRecentlyCreated ?? false) { $rCount++; }
                $r2 = $this->ensureRideByIdentifier($classification, 'Orange Paddle', $createdAt, $updatedAt);
                if ($r2->wasRecentlyCreated ?? false) { $rCount++; }
                continue; // handled mapping for this row
            }

            // Map legacy Paddle_Board -> Paddle Board with Big/Small/Rubber and identifiers
            if ($this->isLegacyPaddleBoard((string) $rideTypeName)) {
                $rideType = $this->ensureRideType('Paddle Board');
                if ($rideType->wasRecentlyCreated ?? false) { $rtCount++; }

                $targetClassificationName = $this->mapLegacyPaddleBoardClassification((string) $classificationName);
                $targetIdentifier = $this->mapLegacyPaddleBoardIdentifier((string) $classificationName);

                $classification = Classification::withTrashed()
                    ->where('ride_type_id', $rideType->id)
                    ->where('name', $targetClassificationName)
                    ->first();
                if (!$classification) {
                    $classification = new Classification();
                    $classification->ride_type_id = $rideType->id;
                    $classification->name = $targetClassificationName;
                    $classification->price_per_hour = is_numeric($pricePerHour) ? (float) $pricePerHour : 0.0;
                    $classification->created_at = $createdAt ?: now();
                    $classification->updated_at = $updatedAt ?: now();
                    $classification->save();
                    $cCount++;
                } else {
                    // Update price from legacy row
                    $classification->price_per_hour = is_numeric($pricePerHour) ? (float) $pricePerHour : $classification->price_per_hour;
                    $classification->save();
                }

                // Ensure ride exists with mapped identifier
                $ride = $this->ensureRideByIdentifier($classification, $targetIdentifier, $createdAt, $updatedAt);
                if ($ride->wasRecentlyCreated ?? false) { $rCount++; }
                continue;
            }

            // Map legacy Water_Bike -> Water Bike with With/Without Propeller and specific identifiers
            if ($this->isLegacyWaterBike((string) $rideTypeName)) {
                $rideType = $this->ensureRideType('Water Bike');
                if ($rideType->wasRecentlyCreated ?? false) { $rtCount++; }

                [$targetClassificationName, $targetIdentifier] = $this->mapLegacyWaterBike((string) $classificationName);

                $classification = Classification::withTrashed()
                    ->where('ride_type_id', $rideType->id)
                    ->where('name', $targetClassificationName)
                    ->first();
                if (!$classification) {
                    $classification = new Classification();
                    $classification->ride_type_id = $rideType->id;
                    $classification->name = $targetClassificationName;
                    $classification->price_per_hour = is_numeric($pricePerHour) ? (float) $pricePerHour : 0.0;
                    $classification->created_at = $createdAt ?: now();
                    $classification->updated_at = $updatedAt ?: now();
                    $classification->save();
                    $cCount++;
                } else {
                    $classification->price_per_hour = is_numeric($pricePerHour) ? (float) $pricePerHour : $classification->price_per_hour;
                    $classification->save();
                }

                $ride = $this->ensureRideByIdentifier($classification, $targetIdentifier, $createdAt, $updatedAt);
                if ($ride->wasRecentlyCreated ?? false) { $rCount++; }
                continue;
            }

            // Map legacy FOLDING_BED -> Folding Bed (type, classification, identifier all 'Folding Bed')
            if ($this->isLegacyFoldingBed((string) $rideTypeName)) {
                $rideType = $this->ensureRideType('Folding Bed');
                if ($rideType->wasRecentlyCreated ?? false) { $rtCount++; }

                $classification = Classification::withTrashed()
                    ->where('ride_type_id', $rideType->id)
                    ->where('name', 'Folding Bed')
                    ->first();
                if (!$classification) {
                    $classification = new Classification();
                    $classification->ride_type_id = $rideType->id;
                    $classification->name = 'Folding Bed';
                    $classification->price_per_hour = is_numeric($pricePerHour) ? (float) $pricePerHour : 0.0;
                    $classification->created_at = $createdAt ?: now();
                    $classification->updated_at = $updatedAt ?: now();
                    $classification->save();
                    $cCount++;
                } else {
                    $classification->price_per_hour = is_numeric($pricePerHour) ? (float) $pricePerHour : $classification->price_per_hour;
                    $classification->save();
                }

                $ride = $this->ensureRideByIdentifier($classification, 'Folding Bed', $createdAt, $updatedAt);
                if ($ride->wasRecentlyCreated ?? false) { $rCount++; }
                continue;
            }

            // Map legacy Jsk -> Kdn -> Kdn to Life Jacket (type/class/identifier 'Life Jacket')
            if ($this->isLegacyLifeJacketJsk((string) $rideTypeName, (string) $classificationName)) {
                $rideType = $this->ensureRideType('Life Jacket');
                if ($rideType->wasRecentlyCreated ?? false) { $rtCount++; }

                $classification = Classification::withTrashed()
                    ->where('ride_type_id', $rideType->id)
                    ->where('name', 'Life Jacket')
                    ->first();
                if (!$classification) {
                    $classification = new Classification();
                    $classification->ride_type_id = $rideType->id;
                    $classification->name = 'Life Jacket';
                    $classification->price_per_hour = is_numeric($pricePerHour) ? (float) $pricePerHour : 0.0;
                    $classification->created_at = $createdAt ?: now();
                    $classification->updated_at = $updatedAt ?: now();
                    $classification->save();
                    $cCount++;
                } else {
                    $classification->price_per_hour = is_numeric($pricePerHour) ? (float) $pricePerHour : $classification->price_per_hour;
                    $classification->save();
                }

                $ride = $this->ensureRideByIdentifier($classification, 'Life Jacket', $createdAt, $updatedAt);
                if ($ride->wasRecentlyCreated ?? false) { $rCount++; }
                continue;
            }

            // Map legacy Boat classifications: Blue->Big, Pink/Yellow->Small
            if ($this->isLegacyBoat((string) $rideTypeName)) {
                $rideType = $this->ensureRideType('Boat');
                if ($rideType->wasRecentlyCreated ?? false) { $rtCount++; }

                $targetClassificationName = $this->mapLegacyBoatClassification((string) $classificationName);

                $classification = Classification::withTrashed()
                    ->where('ride_type_id', $rideType->id)
                    ->where('name', $targetClassificationName)
                    ->first();
                if (!$classification) {
                    $classification = new Classification();
                    $classification->ride_type_id = $rideType->id;
                    $classification->name = $targetClassificationName;
                    $classification->price_per_hour = is_numeric($pricePerHour) ? (float) $pricePerHour : 0.0;
                    $classification->created_at = $createdAt ?: now();
                    $classification->updated_at = $updatedAt ?: now();
                    $classification->save();
                    $cCount++;
                } else {
                    // Update price from legacy
                    $classification->price_per_hour = is_numeric($pricePerHour) ? (float) $pricePerHour : $classification->price_per_hour;
                    $classification->save();
                }

                // Ensure ride exists using original color as identifier
                $rideIdentifier = $this->mapLegacyBoatRideIdentifier((string) $classificationName);
                $ride = $this->ensureRideByIdentifier($classification, $rideIdentifier, $createdAt, $updatedAt);
                if ($ride->wasRecentlyCreated ?? false) { $rCount++; }
                continue;
            }

            $rideType = $this->ensureRideType((string) $rideTypeName);
            if ($rideType->wasRecentlyCreated ?? false) { $rtCount++; }

            $classification = Classification::withTrashed()
                ->where('ride_type_id', $rideType->id)
                ->where('name', (string) $classificationName)
                ->first();
            if (!$classification) {
                $classification = new Classification();
                $classification->ride_type_id = $rideType->id;
                $classification->name = (string) $classificationName;
                $classification->price_per_hour = is_numeric($pricePerHour) ? (float) $pricePerHour : 0.0;
                $classification->created_at = $createdAt ?: now();
                $classification->updated_at = $updatedAt ?: now();
                $classification->save();
                $cCount++;
            }

            $ride = Ride::withTrashed()
                ->where('classification_id', $classification->id)
                ->where('identifier', $classification->name)
                ->first();
            if (!$ride) {
                $ride = new Ride();
                $ride->classification_id = $classification->id;
                $ride->identifier = $classification->name; // placeholder ride per classification
                $ride->is_active = Ride::STATUS_AVAILABLE;
                $ride->created_at = $createdAt ?: now();
                $ride->updated_at = $updatedAt ?: now();
                $ride->save();
                $rCount++;
            }
        }
        return ['ride_types' => $rtCount, 'classifications' => $cCount, 'rides' => $rCount];
    }

    private function importRentals(array $tuples): int
    {
        // Legacy rides_rental_dbs columns:
        // (id, user, rideType, classification, note, duration, life_jacket_usage, pricePerHour, totalPrice, timeStart, timeEnd, created_at, updated_at, status)
        $count = 0;
        foreach ($tuples as $t) {
            if (count($t) < 14) { continue; }
            [$legacyId, $userName, $rideTypeName, $classificationName, $note, $duration, $lifeJacketUsage, $pricePerHour, $totalPrice, $timeStart, $timeEnd, $createdAt, $updatedAt, $status] = $t;

            // Build start/end from created_at date + timeStart/timeEnd
            $date = $createdAt ? (new DateTimeImmutable((string) $createdAt))->format('Y-m-d') : (new DateTimeImmutable())->format('Y-m-d');
            $startAt = $date . ' ' . ($timeStart ?: '00:00:00');
            $endAt = $date . ' ' . ($timeEnd ?: '00:00:00');

            // Find or create user by legacy name to avoid null user_id
            $user = $userName ? $this->ensureUserFromLegacyName((string) $userName) : null;

            // Ensure rideType -> classification -> ride exist (with Clear Kayak/Boat/Paddle Board mapping)
            if ($this->isLegacyClearKayak((string) $rideTypeName)) {
                $rideType = $this->ensureRideType('Clear Kayak');
                $classification = $this->ensureClassification($rideType, 'Double', (string) $pricePerHour);
                $identifier = $this->mapLegacyClearKayakIdentifier((string) $classificationName);
                $ride = $this->ensureRideByIdentifier($classification, $identifier, $createdAt, $updatedAt);
            } elseif ($this->isLegacyPaddleBoard((string) $rideTypeName)) {
                $rideType = $this->ensureRideType('Paddle Board');
                $targetClassificationName = $this->mapLegacyPaddleBoardClassification((string) $classificationName);
                $classification = $this->ensureClassification($rideType, $targetClassificationName, (string) $pricePerHour);
                $identifier = $this->mapLegacyPaddleBoardIdentifier((string) $classificationName);
                $ride = $this->ensureRideByIdentifier($classification, $identifier, $createdAt, $updatedAt);
            } elseif ($this->isLegacyBoat((string) $rideTypeName)) {
                $rideType = $this->ensureRideType('Boat');
                $targetClassificationName = $this->mapLegacyBoatClassification((string) $classificationName);
                $classification = $this->ensureClassification($rideType, $targetClassificationName, (string) $pricePerHour);
                $boatIdentifier = $this->mapLegacyBoatRideIdentifier((string) $classificationName);
                $ride = $this->ensureRideByIdentifier($classification, $boatIdentifier, $createdAt, $updatedAt);
            } elseif ($this->isLegacyWaterBike((string) $rideTypeName)) {
                $rideType = $this->ensureRideType('Water Bike');
                [$targetClassificationName, $targetIdentifier] = $this->mapLegacyWaterBike((string) $classificationName);
                $classification = $this->ensureClassification($rideType, $targetClassificationName, (string) $pricePerHour);
                $ride = $this->ensureRideByIdentifier($classification, $targetIdentifier, $createdAt, $updatedAt);
            } elseif ($this->isLegacyFoldingBed((string) $rideTypeName)) {
                $rideType = $this->ensureRideType('Folding Bed');
                $classification = $this->ensureClassification($rideType, 'Folding Bed', (string) $pricePerHour);
                $ride = $this->ensureRideByIdentifier($classification, 'Folding Bed', $createdAt, $updatedAt);
            } elseif ($this->isLegacyLifeJacketJsk((string) $rideTypeName, (string) $classificationName)) {
                $rideType = $this->ensureRideType('Life Jacket');
                $classification = $this->ensureClassification($rideType, 'Life Jacket', (string) $pricePerHour);
                $ride = $this->ensureRideByIdentifier($classification, 'Life Jacket', $createdAt, $updatedAt);
            } else {
                $rideType = $this->ensureRideType((string) $rideTypeName);
                $classification = $this->ensureClassification($rideType, (string) $classificationName, (string) $pricePerHour);
                $ride = $this->ensureRide($classification);
            }

            $rental = new Rental();
            $rental->user_id = $user ? $user->id : null;
            $rental->ride_id = $ride->id;
            $rental->status = $this->mapLegacyStatus((int) $status);
            $rental->start_at = $startAt;
            $rental->end_at = $endAt;
            $rental->duration_minutes = is_null($duration) ? null : (int) $duration;
            $rental->life_jacket_quantity = is_null($lifeJacketUsage) ? 0 : (int) $lifeJacketUsage;
            $rental->note = $note ? (string) $note : null;
            $rental->user_name_at_time = $userName ? (string) $userName : null;
            $rental->ride_identifier_at_time = $ride->identifier;
            $rental->classification_name_at_time = $classification->name;
            $rental->price_per_hour_at_time = (float) $pricePerHour;
            $rental->computed_total = (float) $totalPrice;
            $rental->created_at = $createdAt ? (string) $createdAt : now();
            $rental->updated_at = $updatedAt ? (string) $updatedAt : now();

            $rental->save();
            $count++;
        }
        return $count;
    }

    private function ensureUserFromLegacyName(string $name): ?User
    {
        $trimmed = trim($name);
        if ($trimmed === '') { return null; }
        $existing = User::withTrashed()->where('name', $trimmed)->first();
        if ($existing) { return $existing; }

        $user = new User();
        $user->name = $trimmed;
        // Generate deterministic username base and unique variant
        $usernameBase = $this->generateUsername($trimmed, null);
        $user->username = $this->uniqueUsername($usernameBase);
        // Create a synthetic unique email for legacy-created users
        $emailSlug = Str::slug($trimmed, '.');
        $uniqueSuffix = Str::lower(Str::random(6));
        $user->email = $emailSlug . '.' . $uniqueSuffix . '@legacy.local';
        $user->userType = 'user';
        $user->password = Hash::make(Str::random(24));
        $user->created_at = now();
        $user->updated_at = now();
        $user->save();
        return $user;
    }

    private function isLegacyClearKayak(string $rideTypeName): bool
    {
        return trim($rideTypeName) === 'Clear_Kayak';
    }

    private function mapLegacyClearKayakIdentifier(string $legacyClassification): string
    {
        $map = [
            'BLACK_PADDLE' => 'Black Paddle',
            'ORANGE_PADDLE' => 'Orange Paddle',
            'Orange_Rope' => 'Orange Paddle',
            'Red_Rope' => 'Black Paddle',
        ];
        return $map[$legacyClassification] ?? 'Black Paddle';
    }

    private function isLegacyBoat(string $rideTypeName): bool
    {
        return trim($rideTypeName) === 'Boat';
    }

    private function mapLegacyBoatClassification(string $legacyClassification): string
    {
        $legacy = trim($legacyClassification);
        if ($legacy === 'Blue') { return 'Big'; }
        if ($legacy === 'Pink' || $legacy === 'Yellow') { return 'Small'; }
        return $legacy; // default passthrough
    }

    private function mapLegacyBoatRideIdentifier(string $legacyClassification): string
    {
        $legacy = trim($legacyClassification);
        if (in_array($legacy, ['Blue', 'Pink', 'Yellow'])) {
            return $legacy; // keep color as ride identifier
        }
        return $legacy; // fallback
    }

    private function isLegacyPaddleBoard(string $rideTypeName): bool
    {
        return trim($rideTypeName) === 'Paddle_Board';
    }

    private function mapLegacyPaddleBoardClassification(string $legacyClassification): string
    {
        $legacy = trim($legacyClassification);
        if ($legacy === 'Yellow_Big') { return 'Big'; }
        if (in_array($legacy, ['Blue', 'Gray', 'Pink', 'Yellow'])) { return 'Small'; }
        if ($legacy === 'RUBBER_BLUE') { return 'Rubber'; }
        return $legacy;
    }

    private function mapLegacyPaddleBoardIdentifier(string $legacyClassification): string
    {
        $legacy = trim($legacyClassification);
        if ($legacy === 'Yellow_Big') { return 'Yellow'; }
        if (in_array($legacy, ['Blue', 'Gray', 'Pink', 'Yellow'])) { return $legacy; }
        if ($legacy === 'RUBBER_BLUE') { return 'Blue'; }
        return $legacy;
    }

    private function isLegacyWaterBike(string $rideTypeName): bool
    {
        return trim($rideTypeName) === 'Water_Bike';
    }

    // Returns [classificationName, identifier]
    private function mapLegacyWaterBike(string $legacyClassificationOrColor): array
    {
        $token = trim($legacyClassificationOrColor);
        // If dump holds just color, decide classification by allowed sets
        $with = ['Yellow', 'Red', 'Blue'];
        $without = ['Green', 'Orange'];
        if (in_array($token, $with, true)) {
            return ['With Propeller', $token];
        }
        if (in_array($token, $without, true)) {
            return ['Without Propeller', $token];
        }
        // If it's already one of the new classification tokens, default an identifier placeholder
        if ($token === 'With Propeller') { return ['With Propeller', 'Yellow']; }
        if ($token === 'Without Propeller') { return ['Without Propeller', 'Green']; }
        // Fallback: assume With Propeller
        return ['With Propeller', $token ?: 'Yellow'];
    }

    private function isLegacyFoldingBed(string $rideTypeName): bool
    {
        return trim($rideTypeName) === 'FOLDING_BED';
    }

    private function isLegacyLifeJacketJsk(string $rideTypeName, string $classificationName): bool
    {
        return trim($rideTypeName) === 'Jsk' && trim($classificationName) === 'Kdn';
    }

    private function ensureRideByIdentifier(Classification $classification, string $identifier, $createdAt = null, $updatedAt = null): Ride
    {
        $ride = Ride::withTrashed()
            ->where('classification_id', $classification->id)
            ->where('identifier', $identifier)
            ->first();
        if ($ride) { return $ride; }
        $ride = new Ride();
        $ride->classification_id = $classification->id;
        $ride->identifier = $identifier;
        $ride->is_active = Ride::STATUS_AVAILABLE;
        $ride->created_at = $createdAt ?: now();
        $ride->updated_at = $updatedAt ?: now();
        $ride->save();
        return $ride;
    }

    private function mapLegacyStatus(int $legacy): int
    {
        // Legacy default appears to be 1; map 1 -> completed, else 0 active
        return $legacy === 1 ? Rental::STATUS_COMPLETED : Rental::STATUS_ACTIVE;
    }

    private function ensureRideType(string $name): RideType
    {
        $rt = RideType::withTrashed()->where('name', $name)->first();
        if ($rt) { return $rt; }
        $rt = new RideType();
        $rt->name = $name;
        $rt->save();
        return $rt;
    }

    private function ensureClassification(RideType $rideType, string $name, string $pricePerHour): Classification
    {
        $c = Classification::withTrashed()->where('ride_type_id', $rideType->id)->where('name', $name)->first();
        if ($c) { return $c; }
        $c = new Classification();
        $c->ride_type_id = $rideType->id;
        $c->name = $name;
        $c->price_per_hour = is_numeric($pricePerHour) ? (float) $pricePerHour : 0.0;
        $c->save();
        return $c;
    }

    private function ensureRide(Classification $classification): Ride
    {
        // Use a stable identifier derived from classification name
        $identifier = $classification->name;
        $ride = Ride::withTrashed()->where('classification_id', $classification->id)->where('identifier', $identifier)->first();
        if ($ride) { return $ride; }
        $ride = new Ride();
        $ride->classification_id = $classification->id;
        $ride->identifier = $identifier;
        $ride->is_active = Ride::STATUS_AVAILABLE;
        $ride->save();
        return $ride;
    }

    private function generateUsername(string $name, ?string $email): string
    {
        $base = Str::slug($name, '_');
        if (!$base && $email) {
            $base = Str::before($email, '@');
        }
        if (!$base) {
            $base = 'user_' . Str::random(6);
        }
        return $base;
    }

    private function uniqueUsername(string $base, ?int $ignoreId = null): string
    {
        $candidate = $base;
        $i = 1;
        while (true) {
            $query = User::withTrashed()->where('username', $candidate);
            if ($ignoreId) { $query->where('id', '!=', $ignoreId); }
            if (!$query->exists()) {
                return $candidate;
            }
            $candidate = $base . '_' . $i;
            $i++;
        }
    }
}


