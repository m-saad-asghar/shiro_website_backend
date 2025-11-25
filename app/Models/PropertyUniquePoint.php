<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropertyUniquePoint extends Model
{
    protected $fillable = [
        'property_id',
        'point_text',
        'icon_url',
        'sort_order'
    ];

    protected $casts = [
        'property_id' => 'integer',
        'sort_order' => 'integer'
    ];

    // العلاقة مع Property
    public function property()
    {
        return $this->belongsTo(Property::class);
    }
}
