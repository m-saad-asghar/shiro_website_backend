<?php

namespace App\Models;

use App\Models\BaseModel;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Review extends BaseModel
{protected $fillable = [
        'image' => 'image',
        'name' => 'name',
        'rate' => 'rate',
        'title' => 'title',
        'description' => 'description',
        'date' => 'date',
    ];

protected $casts = [
        'rate' => 'float',
        'date' => 'datetime',
    ];

    protected $translatable = [
        'title',
        'description',
        'name'
    ];
}
