<?php

namespace App\Models;

use App\Models\BaseModel;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class ContactUs extends BaseModel
{
    protected $fillable = [
        'email' => 'email',
        'phone' => 'phone',
        'secondary_phone' => 'secondary_phone',
        'whatsapp' => 'whatsapp',
        'fax' => 'fax',
        'location' => 'location',
        'latitude' => 'latitude',
        'longitude' => 'longitude',
        'map_iframe' => 'map_iframe',
        'work_hours' => 'work_hours',
        'facebook' => 'facebook',
        'instagram' => 'instagram',
        'twitter' => 'twitter',
        'linkedin' => 'linkedin',
        'tiktok' => 'tiktok',
        'video'=>'video',
        'office'=>'office'
    ];

protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
         'office'=>'array',
    ];

    protected $translatable = [
        'location',
        'work_hours',
        'office'
    ];
}
