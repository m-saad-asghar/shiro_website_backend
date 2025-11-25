<?php

namespace App\Models;

use App\Models\BaseModel;

use Illuminate\Database\Eloquent\Model;

class Faq extends BaseModel
{protected $fillable = [
        'question' => 'question',
        'answer' => 'answer',
    ];

protected $casts = [
    ];

protected $translatable=[
    'question',
    'answer',

];
}
