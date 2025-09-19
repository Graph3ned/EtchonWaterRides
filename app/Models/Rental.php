<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\LogsActivity;

class Rental extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'user_id',
        'ride_id',
        'status',
        'start_at',
        'end_at',
        'duration_minutes',
        'life_jacket_quantity',
        'note',
        'user_name_at_time',
        'ride_identifier_at_time',
        'classification_name_at_time',
        'ride_type_name_at_time',
        'price_per_hour_at_time',
        'computed_total',
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'price_per_hour_at_time' => 'decimal:2',
        'computed_total' => 'decimal:2',
    ];

    // Status constants
    const STATUS_ACTIVE = 0;
    const STATUS_COMPLETED = 1;
    const STATUS_CANCELLED = 2;

    /**
     * Get the user that owns the rental.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the ride that was rented.
     */
    public function ride(): BelongsTo
    {
        return $this->belongsTo(Ride::class);
    }

    // Note: Access classification via nested relation: $this->ride->classification

    // Note: Access ride type via nested relation: $this->ride->classification->rideType

    /**
     * Scope for filtering by user.
     */
    public function scopeFilterByUser($query, $userId)
    {
        if ($userId) {
            $query->where('user_id', $userId);
        }
        return $query;
    }

    /**
     * Scope for filtering by ride type.
     */
    public function scopeFilterByRideType($query, $rideTypeId)
    {
        if ($rideTypeId) {
            $query->whereHas('ride.classification', function ($q) use ($rideTypeId) {
                $q->where('ride_type_id', $rideTypeId);
            });
        }
        return $query;
    }

    /**
     * Scope for filtering by classification.
     */
    public function scopeFilterByClassification($query, $classificationId)
    {
        if ($classificationId) {
            $query->whereHas('ride', function ($q) use ($classificationId) {
                $q->where('classification_id', $classificationId);
            });
        }
        return $query;
    }

    /**
     * Scope for active rentals.
     */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    /**
     * Scope for completed rentals.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    /**
     * Scope for cancelled rentals.
     */
    public function scopeCancelled($query)
    {
        return $query->where('status', self::STATUS_CANCELLED);
    }

    /**
     * Calculate duration in minutes.
     */
    public function calculateDuration()
    {
        if ($this->start_at && $this->end_at) {
            return $this->start_at->diffInMinutes($this->end_at);
        }
        return $this->duration_minutes;
    }

    /**
     * Calculate total price.
     */
    public function calculateTotal()
    {
        $duration = $this->calculateDuration();
        $pricePerMinute = $this->price_per_hour_at_time / 60;
        return round($pricePerMinute * $duration, 2);
    }
}