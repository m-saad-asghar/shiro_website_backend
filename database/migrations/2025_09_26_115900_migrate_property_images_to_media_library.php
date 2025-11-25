<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\Property;
use Illuminate\Support\Facades\Storage;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $properties = DB::table('properties')->whereNotNull('images')->get();
        
        foreach ($properties as $property) {
            $propertyModel = Property::find($property->id);
            if (!$propertyModel) continue;
            
            $images = json_decode($property->images, true);
            if (!is_array($images)) continue;
            
            foreach ($images as $imagePath) {
                if (!Storage::disk('public')->exists($imagePath)) continue;
                
                try {
                    $propertyModel->addMediaFromDisk($imagePath, 'public')
                        ->toMediaCollection('images');
                } catch (\Exception $e) {
                    \Log::error("Failed to migrate image for property {$property->id}: " . $e->getMessage());
                }
            }
            
            if (!empty($property->qr_code) && Storage::disk('public')->exists($property->qr_code)) {
                try {
                    $propertyModel->addMediaFromDisk($property->qr_code, 'public')
                        ->toMediaCollection('qr_code');
                } catch (\Exception $e) {
                    \Log::error("Failed to migrate QR code for property {$property->id}: " . $e->getMessage());
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove all media from properties
        $properties = Property::all();
        foreach ($properties as $property) {
            $property->clearMediaCollection('images');
            $property->clearMediaCollection('qr_code');
        }
    }
};
