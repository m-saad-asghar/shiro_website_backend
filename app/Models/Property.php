<?php

namespace App\Models;

use App\Models\BaseModel;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Property extends BaseModel
{
        protected $fillable = [
        'title',
        'meta_title',
        'meta_description',
        'slug',
        'type_id',
        'purpose',
        'is_finish',
        'completion',
        'description',
        'price',
        'starting_price',
        'handover_year',
        'payment_plan',
        'property_mix',
        'rental_period',
        'location',
        'latitude',
        'longitude',
        'map_address',
        'map_embed_url',
        'images',
        'area',
        'region_id',
        'num_bathroom',
        'num_bedroom',
        'agent_id',
        'developer_id',
        'profile',
        'contact',
        'service_id',
        'is_sale',
        'date_sale',
        'is_home',
        'property_type_id',
        'zone_name',
        'dubailand_link',
        'reference_id',
        'qr_code',
        'agent_license',
        'dld_permit_number',
        'broker_license',
        'offplan_pdf'
    ];

    protected $casts = [
        'type_id' => 'integer',
        'is_finish' => 'boolean',
        'price' => 'float',
        'starting_price' => 'float',
        'handover_year' => 'integer',
        'images' => 'array',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'area' => 'float',
        'region_id' => 'integer',
        'num_bathroom' => 'integer',
        'num_bedroom' => 'integer',
        'agent_id' => 'integer',
        'developer_id' => 'integer',
        'contact' => 'array',
        'service_id' => 'integer',
        'is_sale' => 'boolean',
        'date_sale' => 'datetime',
        'is_home' => 'boolean',
        'property_type_id' => 'integer'
    ];

    protected $translatable = [
        'title',
        'meta_title',
        'meta_description',
        'description',
        'profile',
        'location',
        'rental_period',
        'completion',
        'zone_name',
        'purpose'
    ];

    protected $appends = ['converted_price', 'currency_symbol'];

    public function type()
    {
        return $this->belongsTo(Type::class);
    }

    public function propertyType()
    {
        return $this->belongsTo(PropertyType::class);
    }

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }

    public function developer()
    {
        return $this->belongsTo(Developer::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function favoritedBy()
    {
        return $this->belongsToMany(User::class, 'favorites', 'property_id', 'user_id');
    }

    public function amenities()
    {
        return $this->hasMany(PropertyAmenity::class);
    }

    public function floorplans()
    {
        return $this->hasMany(PropertyFloorplan::class);
    }

    public function nearbyPlaces()
    {
        return $this->hasMany(PropertyNearbyPlace::class);
    }

    public function uniquePoints()
    {
        return $this->hasMany(PropertyUniquePoint::class);
    }

    public function paymentSchedules()
    {
        return $this->hasMany(PropertyPaymentSchedule::class);
    }

    public function faqs()
    {
        return $this->hasMany(PropertyFaq::class);
    }

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($property) {
            if (empty($property->slug)) {
                $property->slug = \Str::slug($property->title);
                
                // التأكد من عدم تكرار الـ slug
                $count = static::where('slug', $property->slug)->count();
                if ($count > 0) {
                    $property->slug = $property->slug . '-' . ($count + 1);
                }
            }
        });
        
        static::updating(function ($property) {
            if ($property->isDirty('title') && empty($property->slug)) {
                $property->slug = \Str::slug($property->title);
                
                // التأكد من عدم تكرار الـ slug
                $count = static::where('slug', $property->slug)
                    ->where('id', '!=', $property->id)
                    ->count();
                if ($count > 0) {
                    $property->slug = $property->slug . '-' . ($count + 1);
                }
            }
        });
    }

    public function getConvertedPriceAttribute()
    {
        $defaultPrice = $this->price;

        $currencyCode = request()->header('X-Currency', 'USD'); // الدولار هو الافتراضي
        $currency = Currency::where('title', $currencyCode)->first();

        if (!$currency) {
            return $defaultPrice;
        }

        return round($defaultPrice * $currency->rate, 2);
    }

    public function getCurrencySymbolAttribute()
    {
        $currencyCode = request()->header('X-Currency', 'USD'); // نفس الشي
        $currency = Currency::where('title', $currencyCode)->first();
        return $currency?->symbol ?? '$';
    }

    // Accessor for QR code (للـ Filament - يرجع المسار النسبي)
    public function getQrCodeAttribute($value)
    {
        if (!$value) {
            return null;
        }
        
        // نرجع القيمة الأصلية للـ Filament
        return $value;
    }

    // Accessor for full QR code URL (للـ API - يرجع الرابط الكامل)
    public function getFullQrCodeUrlAttribute()
    {
        $value = $this->attributes['qr_code'] ?? null;
        
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

    // Accessor for images array (للـ Filament - يرجع المسارات النسبية)
    public function getImagesAttribute($value)
    {
        if (!$value) {
            return [];
        }

        $images = is_string($value) ? json_decode($value, true) : $value;
        
        if (!is_array($images)) {
            return [];
        }

        return $images;
    }

    // Accessor for full images URLs (للـ API - يرجع الروابط الكاملة)
    public function getFullImagesUrlsAttribute()
    {
        $images = $this->images; // نستخدم الـ accessor الأصلي
        
        if (empty($images) || !is_array($images)) {
            return [];
        }

        return array_map(function ($image) {
            // إذا كان الرابط كامل بالفعل، نرجعه كما هو
            if (strpos($image, 'http') === 0 || strpos($image, '/storage/') !== false) {
                return $image;
            }
            // نضيف الرابط الكامل
            return asset('storage/' . $image);
        }, $images);
    }

    // Check if property has location data
    public function hasLocation()
    {
        return !empty($this->latitude) && !empty($this->longitude);
    }

    // Get Google Maps link
    public function getGoogleMapsLinkAttribute()
    {
        if ($this->hasLocation()) {
            return "https://www.google.com/maps?q={$this->latitude},{$this->longitude}";
        }
        return null;
    }

}