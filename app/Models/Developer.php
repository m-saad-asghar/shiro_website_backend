<?php

namespace App\Models;

class Developer extends BaseModel
{
    protected $fillable = [
        'name' => 'name',
        'slug' => 'slug',
        'email' => 'email',
        'contact_inf' => 'contact_inf',
        'logo' => 'logo',
        'thumbnail' => 'thumbnail',
        'description' => 'description',
        'description_top'=>'description_top',
        'description_bottom'=>'description_bottom',
    ];

    protected $casts = [
        'contact_inf' => 'array',
    ];
    protected $translatable = [
        'name',
        'contact_inf',
        'description',
        'description_top',
        'description_bottom',
    ];

    public function properties()
    {
        return $this->hasMany(Property::class);
    }

}
