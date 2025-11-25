<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropertyPaymentSchedule extends Model
{
    protected $fillable = [
        'property_id',
        'phase_name',
        'percentage',
        'description',
        'due_date',
        'sort_order'
    ];

    protected $casts = [
        'property_id' => 'integer',
        'percentage' => 'decimal:2',
        'due_date' => 'date',
        'sort_order' => 'integer'
    ];

    // العلاقة مع Property
    public function property()
    {
        return $this->belongsTo(Property::class);
    }
}
