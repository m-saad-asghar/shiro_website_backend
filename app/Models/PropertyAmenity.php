<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropertyAmenity extends Model
{
    protected $fillable = [
        'property_id',
        'name',
        'icon_url',
        'description',
        'sort_order'
    ];

    // Validation rules (all optional except property_id)
    public static function rules()
    {
        return [
            'property_id' => 'required|exists:properties,id',
            'name' => 'nullable|string|max:255',
            'icon_url' => 'nullable|url',
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer|min:0'
        ];
    }

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
