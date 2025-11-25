<?php

namespace App\Models;

use App\Models\BaseModel;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class ContactForm extends BaseModel
{protected $fillable = [
        'name' => 'name',
        'email' => 'email',
        'phone' => 'phone',
        'message' => 'message',
        'language' => 'language',
    ];

protected $casts = [
    ];

    //
}
