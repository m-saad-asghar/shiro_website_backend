<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropertyLead extends Model
{
    protected $fillable = [
        'property_id',
        'name',
        'email',
        'phone',
        'message',
        'interest_type',
        'status',
        'contacted_at'
    ];

    protected $casts = [
        'property_id' => 'integer',
        'contacted_at' => 'datetime'
    ];

    // العلاقة مع Property
    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    // Scopes للفلترة
    public function scopeNew($query)
    {
        return $query->where('status', 'new');
    }

    public function scopeContacted($query)
    {
        return $query->where('status', 'contacted');
    }
}
