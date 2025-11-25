<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Blog extends BaseModel
{
    protected $fillable = [
        'id',
        'title',
        'slug',
        'description',
        'main_image',
        'blog_category_id',
        'is_active',
        'order',
        'meta_description',
        'meta_title'
    ];

    protected $casts = [
        'id' => 'string',
        'title' => 'string',
        'slug' => 'string',
        'description' => 'string',
        'main_image' => 'string',
        'is_active' => 'boolean',
        'order' => 'integer'
    ];

    protected $translatable=['title','description','meta_description','meta_title'];

    public function blog_category()
    {
        return $this->belongsTo(BlogCategory::class);
    }


    public function blogCategory()
    {
        return $this->belongsTo(BlogCategory::class, 'blog_category_id');
    }

    public function getTagsAttribute()
    {
        return generateKeywordsMultilingual($this->title);
    }

    // Accessor for main_image (للـ Filament - يرجع المسار النسبي)
    public function getMainImageAttribute($value)
    {
        if (!$value) {
            return null;
        }
        
        // نرجع القيمة الأصلية للـ Filament
        return $value;
    }

    // Accessor for full main image URL (للـ API - يرجع الرابط الكامل)
    public function getFullMainImageUrlAttribute()
    {
        $value = $this->attributes['main_image'] ?? null;
        
        if (!$value) {
            return null;
        }
        
        // إذا كان الرابط كامل بالفعل، نرجعه كما هو
        if (strpos($value, '/storage/') !== false || strpos($value, 'http') === 0) {
            return $value;
        }
        
        // نضيف الرابط الكامل
        return asset('storage/' . $value);
    }

    protected $appends = ['tags'];
}
