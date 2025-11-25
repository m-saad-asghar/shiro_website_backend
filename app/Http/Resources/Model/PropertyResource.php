<?php

namespace App\Http\Resources\Model;

use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Basic\BasicResource;
use App\Services\Basic\ModelColumnsService;
use Illuminate\Support\Facades\Auth;

class PropertyResource extends BasicResource
{
    public function toArray(Request $request): array
    {
        return $this->initResource(
            ModelColumnsService::getServiceFor(
                Property::class
            )
        );
    }

    protected function initResource($modelColumnsService): array
    {
        $this->result = parent::initResource($modelColumnsService);

        $user = Auth::guard('sanctum')->user();

        // توليد الرابط الآمن بدون IDs
        $frontendUrl = $this->generateFrontendUrl();

        return array_merge($this->result, [
            'agent'      => new AgentResource($this->resource->agent),
            'developer'  => new DeveloperResource($this->resource->developer),
            'property_type'       => new PropertyTypeResource($this->resource->propertyType),
            'meta_title' => $this->resource->meta_title,
            'meta_description' => $this->resource->meta_description,
            'is_favorite' => $user
                ? $this->resource->favoritedBy()->where('user_id', $user->id)->exists()
                : false,
            'converted_price' => number_format($this->resource->converted_price, 0, '.', ','),
            'price' => number_format($this->resource->price, 0, '.', ','),
            'starting_price' => $this->resource->starting_price 
                ? number_format($this->resource->starting_price, 0, '.', ',') 
                : null,
            
            // Images with full URLs for frontend
            'images' => $this->resource->full_images_urls,
            
            // QR Code with full URL for frontend
            'qr_code' => $this->resource->full_qr_code_url,
            
            // Frontend URL without IDs (آمن)
            'frontend_url' => $frontendUrl,
            'detail_url' => $frontendUrl, // alias
            
            // Location & Map data
            'latitude' => $this->resource->latitude,
            'longitude' => $this->resource->longitude,
            'map_address' => $this->resource->map_address,
            'map_embed_url' => $this->resource->map_embed_url,
            'has_location' => $this->resource->hasLocation(),
            'google_maps_link' => $this->resource->google_maps_link,
            
            // Off-Plan specific fields - optional (تُحمّل فقط عند الحاجة)
            'amenities' => $this->when($this->resource->relationLoaded('amenities'), 
                $this->resource->amenities->map(function ($amenity) {
                    return [
                        'id' => $amenity->id,
                        'name' => $amenity->name,
                        'icon_url' => $amenity->icon_url,
                        'description' => $amenity->description,
                        'sort_order' => $amenity->sort_order,
                    ];
                })
            ),
            
            'floorplans' => $this->when($this->resource->relationLoaded('floorplans'),
                $this->resource->floorplans->map(function ($floorplan) {
                    return [
                        'id' => $floorplan->id,
                        'type' => $floorplan->type,
                        'plan_image_url' => $floorplan->plan_image_url,
                        'pdf_url' => $floorplan->pdf_url,
                        'area' => $floorplan->area,
                        'price' => $floorplan->price ? number_format($floorplan->price, 0, '.', ',') : null,
                        'description' => $floorplan->description,
                        'sort_order' => $floorplan->sort_order,
                    ];
                })
            ),
            
            'nearby_places' => $this->when($this->resource->relationLoaded('nearbyPlaces'),
                $this->resource->nearbyPlaces->map(function ($place) {
                    return [
                        'id' => $place->id,
                        'place_name' => $place->place_name,
                        'time_minutes' => $place->time_minutes,
                        'distance' => $place->distance,
                        'transport_type' => $place->transport_type,
                        'sort_order' => $place->sort_order,
                    ];
                })
            ),
            
            'unique_points' => $this->when($this->resource->relationLoaded('uniquePoints'),
                $this->resource->uniquePoints->map(function ($point) {
                    return [
                        'id' => $point->id,
                        'point_text' => $point->point_text,
                        'icon_url' => $point->icon_url,
                        'sort_order' => $point->sort_order,
                    ];
                })
            ),
            
            'payment_schedules' => $this->when($this->resource->relationLoaded('paymentSchedules'),
                $this->resource->paymentSchedules->map(function ($schedule) {
                    return [
                        'id' => $schedule->id,
                        'phase_name' => $schedule->phase_name,
                        'percentage' => $schedule->percentage,
                        'description' => $schedule->description,
                        'due_date' => $schedule->due_date?->format('Y-m-d'),
                        'sort_order' => $schedule->sort_order,
                    ];
                })
            ),
            
            'faqs' => $this->when($this->resource->relationLoaded('faqs'),
                $this->resource->faqs->map(function ($faq) {
                    return [
                        'id' => $faq->id,
                        'question' => $faq->question,
                        'answer' => $faq->answer,
                        'sort_order' => $faq->sort_order,
                    ];
                })
            ),
            
            'created_at' => $this->resource->created_at,
            'updated_at' => $this->resource->updated_at,
        ]);

    }

    /**
     * توليد رابط آمن بنص عام بدلاً من IDs
     */
    private function generateFrontendUrl()
    {
        $baseUrl = 'https://shiroproperties.com';
        $slug = $this->resource->slug;
        
        // تحديد القسم بناءً على purpose
        $purpose = strtolower($this->resource->purpose ?? '');
        
        // بناء الرابط مع نص عام ثابت
        if (str_contains($purpose, 'sale') || str_contains($purpose, 'buy')) {
            return "{$baseUrl}/buy/shiro-estate-buy-properties/{$slug}";
        } elseif (str_contains($purpose, 'rent')) {
            return "{$baseUrl}/rent/shiro-estate-rent-properties/{$slug}";
        } elseif (str_contains($purpose, 'off') || str_contains($purpose, 'plan') || str_contains($purpose, 'project')) {
            return "{$baseUrl}/new-projects/shiro-estate-offplan-projects/{$slug}";
        }
        
        // افتراضي
        return "{$baseUrl}/property/shiro-estate-properties/{$slug}";
    }
}
