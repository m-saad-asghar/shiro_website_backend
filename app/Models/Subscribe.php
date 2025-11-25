<?php

namespace App\Models;

use App\Models\BaseModel;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Subscribe extends BaseModel
{protected $fillable = [
        'name' => 'name',
        'email' => 'email',
    ];

protected $casts = [
    ];

    //
}
