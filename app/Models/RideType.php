<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class RideType extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
    ];

    /**
     * Get the classifications for the ride type.
     */
    public function classifications(): HasMany
    {
        return $this->hasMany(Classification::class);
    }

    /**
     * Get all rides through classifications.
     */
    public function rides()
    {
        return $this->hasManyThrough(Ride::class, Classification::class);
    }
}
