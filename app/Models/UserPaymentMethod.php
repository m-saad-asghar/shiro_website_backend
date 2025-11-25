<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPaymentMethod extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'user_id'=>'user_id',
        'payment_method'=>'payment_method',
        'payment_method_name'=>'payment_method_name',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
