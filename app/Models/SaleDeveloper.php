<?php

namespace App\Models;

use App\Models\BaseModel;

use Illuminate\Database\Eloquent\Model;

class SaleDeveloper extends BaseModel
{protected $fillable = [
        'developer_id' => 'developer_id',
        'property_id' => 'property_id',
        'price' => 'price',
        'date' => 'date',
    ];

protected $casts = [
        'developer_id' => 'integer',
        'property_id' => 'integer',
        'price' => 'float',
        'date' => 'datetime',
    ];



}
