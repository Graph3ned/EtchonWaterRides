<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'username',
        'userType',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    /**
     * Get the rentals for the user.
     */
    public function rentals(): HasMany
    {
        return $this->hasMany(Rental::class);
    }

    /**
     * Get the staff logs for the user.
     */
    public function staffLogs(): HasMany
    {
        return $this->hasMany(StaffLog::class);
    }

    /**
     * Get the profile change requests for the user.
     */
    public function profileChangeRequests(): HasMany
    {
        return $this->hasMany(ProfileChangeRequest::class);
    }

    /**
     * Check if user is admin.
     */
    public function isAdmin(): bool
    {
        return $this->userType === '1' || $this->userType === 1;
    }

    /**
     * Check if user is staff.
     */
    public function isStaff(): bool
    {
        return $this->userType === '0' || $this->userType === 0;
    }
}
