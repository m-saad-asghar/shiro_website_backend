<?php

namespace App\Models;

use App\Models\BaseModel;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Type extends BaseModel
{
    protected $fillable = [
        'name' => 'name',
        'for_agent' => 'for_agent',
        'for_developer' => 'for_developer',
    ];

protected $casts = [
        'for_agent' => 'boolean',
        'for_developer' => 'boolean',
    ];

    protected $translatable = [
        'name',
    ];
}
