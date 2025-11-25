<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropertyNearbyPlace extends Model
{
    protected $fillable = [
        'property_id',
        'place_name',
        'time_minutes',
        'distance',
        'transport_type',
        'sort_order'
    ];

    protected $casts = [
        'property_id' => 'integer',
        'time_minutes' => 'integer',
        'sort_order' => 'integer'
    ];

    // العلاقة مع Property
    public function property()
    {
        return $this->belongsTo(Property::class);
    }
}
