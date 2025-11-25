<?php

namespace App\Models;

use App\Models\BaseModel;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class AboutUs extends BaseModel
{
    protected $fillable = [
        'manager_name' => 'manager_name',
        'manager_position' => 'manager_position',
        'manager_image' => 'manager_image',
        'manager_description' => 'manager_description',
        'video_url' => 'video_url',
        'title' => 'title',
        'sub_description' => 'sub_description',
        'description' => 'description',
        'content' => 'content',
        'vision' => 'vision',
        'mission' => 'mission',
        'apart' => 'apart',
        'Our_value' => 'Our_value',
        'approach' => 'approach',
        'target' => 'target',
        'philosophy' => 'philosophy',
        'text_partner' => 'text_partner',
        'text_services' => 'text_services',
    ];
    protected $translatable = [
        'manager_name',
        'manager_position',
        'manager_description',
        'title',
        'sub_description',
        'description',
        'content',
        'vision',
        'mission',
        'apart',
        'Our_value',
        'approach',
        'target',
        'philosophy',
        'text_partner',
        'text_services',
    ];


protected $casts = [
        'Our_value' => 'array',
        'target' => 'array',
    ];

    //
}
