<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactAgentForm extends BaseModel
{
    protected $fillable = [
        'first_name',
        'second_name',
        'phone_one',
        'phone_two',
        'message',
        'agent_id',
        'property_id',
    ];

    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }

    public function property()
    {
        return $this->belongsTo(Property::class);
    }
}
