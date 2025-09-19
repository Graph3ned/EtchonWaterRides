<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class prices extends Model
{
    protected $fillable = [
    'ride_type',
    'classification',
    'price_per_hour',
    ];
}
