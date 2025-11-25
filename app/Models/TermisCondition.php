<?php

namespace App\Models;

use App\Models\BaseModel;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class TermisCondition extends BaseModel
{protected $fillable = [
        'name' => 'name',
        'description' => 'description',
    ];

protected $casts = [
    ];


    protected $translatable = [
        'name',
        'description',
    ];
}
