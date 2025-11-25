<?php

namespace App\Models;

use App\Models\BaseModel;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Service extends BaseModel
{
    protected $fillable = [
        'title_main' => 'title_main',
        'image_main' => 'image_main',
        'description' => 'description',
        'sub_image' => 'sub_image',
        'sub_title' => 'sub_title',
        'description_header' => 'description_header',
    ];
    protected $translatable = [
        'title_main',
        'description',
        'sub_title',
        'description_header',
    ];

protected $casts = [
    ];

    public function properties()
    {
        return $this->hasMany(Property::class);
    }

}
