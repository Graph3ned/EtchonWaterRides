<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProfileChangeRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'new_email',
        'otp_code_hash',
        'payload',
        'expires_at',
        'consumed_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'expires_at' => 'datetime',
        'consumed_at' => 'datetime',
    ];

    /**
     * Get the user that owns the profile change request.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}