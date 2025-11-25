<?php

namespace App\Models;

use App\Models\BaseModel;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Slider extends BaseModel
{protected $fillable = [
        'page' => 'page',
        'image' => 'image',
        'title' => 'title',
        'video' => 'video',
    ];

protected $casts = [
    ];

    protected $translatable = [
        'title',
    ];
}
