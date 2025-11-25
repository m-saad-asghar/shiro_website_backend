<?php

namespace App\Models;

class Region extends BaseModel
{
    protected $fillable = [
        'name' => 'name',
        'image' => 'image',
        'description' => 'description',
    ];

    protected $casts = [
    ];


    protected $translatable = [
        'name',
        'description',
    ];
}
