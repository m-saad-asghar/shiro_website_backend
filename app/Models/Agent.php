<?php

namespace App\Models;

class Agent extends BaseModel
{
    protected $translatable = [
        'name',
        'address',
        'contact_inf',
        'description',
    ];
    protected $fillable = [
        'name' => 'name',
        'address' => 'address',
        'contact_inf' => 'contact_inf',
        'email' => 'email',
        'image'=>'image',
        'description'=>'description',
    ];

    protected $casts = [
        'contact_inf' => 'array',
    ];

    public function properties()
    {
        return $this->hasMany(Property::class);
    }

    public function sales()
    {
        return $this->hasMany(\App\Models\SaleAgent::class, 'agent_id');
    }

    public function ContactAgentForms()
    {
        return $this->hasMany(\App\Models\ContactAgentForm::class, 'agent_id');
    }


}
