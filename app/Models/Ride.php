<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ride extends Model
{
    use HasFactory, SoftDeletes;

    // Ride status constants
    const STATUS_INACTIVE = 0;     // Inactive/disabled
    const STATUS_AVAILABLE = 1;    // Active and available for rental
    const STATUS_USED = 2;         // Active but currently being used

    protected $fillable = [
        'classification_id',
        'identifier',
        'is_active',
        'image_path',
    ];

    protected $casts = [
        'is_active' => 'integer',
    ];

    /**
     * Get the classification that owns the ride.
     */
    public function classification(): BelongsTo
    {
        return $this->belongsTo(Classification::class);
    }

    // Note: Access ride type via nested relation: $this->classification->rideType

    /**
     * Get the rentals for the ride.
     */
    public function rentals(): HasMany
    {
        return $this->hasMany(Rental::class);
    }

    /**
     * Scope for available rides (status = 1).
     */
    public function scopeAvailable($query)
    {
        return $query->where('is_active', self::STATUS_AVAILABLE);
    }

    /**
     * Scope for used rides (status = 2).
     */
    public function scopeUsed($query)
    {
        return $query->where('is_active', self::STATUS_USED);
    }

    /**
     * Scope for inactive rides (status = 3).
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', self::STATUS_INACTIVE);
    }

    /**
     * Scope for active rides (available or used).
     */
    public function scopeActive($query)
    {
        return $query->whereIn('is_active', [self::STATUS_AVAILABLE, self::STATUS_USED]);
    }

    /**
     * Check if ride is currently available.
     */
    public function isAvailable()
    {
        return $this->is_active === self::STATUS_AVAILABLE;
    }

    /**
     * Check if ride is currently being used.
     */
    public function isUsed()
    {
        return $this->is_active === self::STATUS_USED;
    }

    /**
     * Check if ride is inactive.
     */
    public function isInactive()
    {
        return $this->is_active === self::STATUS_INACTIVE;
    }
}
