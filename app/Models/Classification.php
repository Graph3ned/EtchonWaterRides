<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Classification extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'ride_type_id',
        'name',
        'price_per_hour',
    ];

    protected $casts = [
        'price_per_hour' => 'decimal:2',
    ];

    /**
     * Get the ride type that owns the classification.
     */
    public function rideType(): BelongsTo
    {
        return $this->belongsTo(RideType::class);
    }

    /**
     * Get the rides for the classification.
     */
    public function rides(): HasMany
    {
        return $this->hasMany(Ride::class);
    }

    /**
     * Get all rentals through rides.
     */
    public function rentals()
    {
        return $this->hasManyThrough(Rental::class, Ride::class);
    }
}
