<?php

namespace App\Models;

class SaleAgent extends BaseModel
{
    protected $fillable = [
        'agent_id' => 'agent_id',
        'property_id' => 'property_id',
        'price' => 'price',
        'date' => 'date',
        'total_paid' => 'total_paid',
        'status' => 'status',
        'user_id' => 'user_id',
    ];

    protected $casts = [
        'agent_id' => 'integer',
        'property_id' => 'integer',
        'price' => 'float',
        'date' => 'datetime',
        'total_paid' => 'decimal:2',
        'status' => 'string',
    ];

    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }

    public function property()
    {
        return $this->belongsTo(Property::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function userPayments()
    {
        return $this->hasMany(SaleAgentPayment::class, 'sale_agent_id');  // تأكد من تعديل المفتاح إذا كان مختلف
    }
}
