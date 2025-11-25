<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropertyFloorplan extends Model
{
    protected $fillable = [
        'property_id',
        'type',
        'plan_image_url',
        'pdf_url',
        'area',
        'price',
        'description',
        'sort_order'
    ];

    protected $casts = [
        'property_id' => 'integer',
        'area' => 'decimal:2',
        'price' => 'decimal:2',
        'sort_order' => 'integer'
    ];

    // Validation rules (all optional except property_id)
    public static function rules()
    {
        return [
            'property_id' => 'required|exists:properties,id',
            'type' => 'nullable|string|max:255',
            'plan_image_url' => 'nullable|string',
            'pdf_url' => 'nullable|string',
            'area' => 'nullable|numeric|min:0',
            'price' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer|min:0'
        ];
    }

    // العلاقة مع Property
    public function property()
    {
        return $this->belongsTo(Property::class);
    }
}
