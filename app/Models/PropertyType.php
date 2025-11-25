<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropertyType extends BaseModel
{
    protected $fillable = [
        'name',
    ];

    protected $casts = [
        'for_agent' => 'boolean',
        'for_developer' => 'boolean',
    ];

    protected $translatable = [
        'name',
    ];
}
