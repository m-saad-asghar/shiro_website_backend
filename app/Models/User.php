<?php

namespace App\Models;

use App\Models\BaseAuthModel;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends BaseAuthModel
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name' => 'name',
        'email' => 'email',
        'password' => 'password',
        'register_id' => 'register_id',
        'address' => 'address',
        'birthday' => 'birthday',
        'phone' => 'phone',
        'gender' => 'gender',
        'image_profile' => 'image_profile',
        'status' => 'status',
        'email_verified_at' => 'email_verified_at',
        'remember_token' => 'remember_token',
        'custom_fields' => 'custom_fields',
        'avatar_url' => 'avatar_url',
        'customer_id'=>'customer_id',
        'register_method'=>'register_method'

    ];

protected $casts = [
        'register_id' => 'string',
        'birthday' => 'datetime',
        'status' => 'integer',
        'email_verified_at' => 'datetime',
        'custom_fields' => 'array',
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
    public function userPaymentMethods()
    {
        return $this->hasMany(UserPaymentMethod::class);
    }

    public function favorites()
    {
        return $this->belongsToMany(Property::class, 'favorites', 'user_id', 'propertly_id');
    }
}
