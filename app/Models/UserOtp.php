<?php

namespace App\Models;

use App\Models\BaseModel;

use Illuminate\Database\Eloquent\Model;

class UserOtp extends BaseModel
{protected $fillable = [
        'user_id' => 'user_id',
        'otp' => 'otp',
        'isVerified' => 'isVerified',
        'verified_at' => 'verified_at',
        'type' => 'type',
       'email'=>'email'
    ];

protected $casts = [
        'user_id' => 'integer',
        'isVerified' => 'boolean',
        'verified_at' => 'datetime',
    ];

    //
}
