<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaleAgentPayment extends BaseModel
{
    protected $fillable = [
        'sale_agent_id',
        'user_id',
        'amount',
        'status',
        'stripe_payment_intent_id',
        'stripe_checkout_session_id',
        'paid_at',
        'note',
    ];

    protected $casts = [
        'amount' => 'float',
        'paid_at' => 'datetime',
    ];

    public function saleAgent()
    {
        return $this->belongsTo(SaleAgent::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected static function booted()
    {
        static::creating(function ($payment) {
            if (! $payment->user_id && $payment->saleAgent) {
                $payment->user_id = $payment->saleAgent->user_id;
            }
        });
    }

}
