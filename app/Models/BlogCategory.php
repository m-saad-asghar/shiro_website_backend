<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlogCategory extends BaseModel
{
    protected $fillable = [
        'id' => 'id',
        'title' => 'title'
    ];

    protected $casts = [
        'id' => 'string',
        'title' => 'string'
    ];

    protected $translatable=['title'];

    public function blogs()
    {
        return $this->hasMany(Blog::class);
    }

}
