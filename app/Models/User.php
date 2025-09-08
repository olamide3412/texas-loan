<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
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
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function staff(){
        return $this->hasOne(Staff::class);
    }

     // Orders created by user
    public function createdOrders(): HasMany
    {
        return $this->hasMany(Order::class, 'created_by');
    }

    public function approvedOrders(): HasMany
    {
        return $this->hasMany(Order::class, 'approved_by');
    }

    public function rejectedOrders(): HasMany
    {
        return $this->hasMany(Order::class, 'rejected_by');
    }

    public function givenOrders(): HasMany
    {
        return $this->hasMany(Order::class, 'given_by');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(Log::class);
    }
}
