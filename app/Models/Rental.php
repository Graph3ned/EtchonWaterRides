<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\LogsActivity;

class Rental extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'rentals';

    protected $fillable = [
        'user',
        'rideType',
        'classification',
        'note',
        'pricePerHour',
        'duration',
        'life_jacket_usage',
        'timeStart',
        'timeEnd',
        'totalPrice',
        'status',
    ];

    /**
     * Scope for filtering by user.
     */
    public function scopeFilterByUser($query, $user)
    {
        if ($user) {
            $query->where('user', $user);
        }
        return $query;
    }

    /**
     * Scope for filtering by ride type.
     */
    public function scopeFilterByRideType($query, $rideType)
    {
        if ($rideType) {
            $query->where('rideType', $rideType);
        }
        return $query;
    }

    /**
     * Scope for filtering by classification.
     */
    public function scopeFilterByClassification($query, $classification)
    {
        if ($classification) {
            $query->where('classification', $classification);
        }
        return $query;
    }
}
