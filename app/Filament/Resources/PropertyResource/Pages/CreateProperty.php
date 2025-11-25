<?php

namespace App\Filament\Resources\PropertyResource\Pages;

use App\Filament\Resources\PropertyResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateProperty extends CreateRecord
{
    use CreateRecord\Concerns\Translatable;
    protected static string $resource = PropertyResource::class;

    protected function getHeaderActions(): array
    {
        return [
             Actions\LocaleSwitcher::make(),

        ];
    }

    protected function getRedirectUrl() : string
    {
        return $this->getResource()::getUrl("index");
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (isset($data['contact']) && is_array($data['contact'])) {
            $data['contact'] = collect($data['contact'])->map(function ($item) {
                return [
                    'type' => $item['type'] ?? '',
                    'value' => $item['value'] ?? '',
                ];
            })->values()->toArray();
        }

        return $data;
    }

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        // التحقق من وجود 3 صور على الأقل فقط إذا كان العقار للبيع
        $isSale = $data['is_sale'] ?? false;
        
        if ($isSale) {
            $images = $data['images'] ?? [];
            if (!is_array($images)) {
                $images = [];
            }
            
            // حساب عدد الصور الحقيقي (تجاهل القيم الفارغة)
            $imageCount = count(array_filter($images, fn($img) => !empty($img)));
            
            if ($imageCount < 3) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'images' => ['يجب إضافة 3 صور على الأقل للعقار عندما يكون معروض للبيع.'],
                ]);
            }
        }

        return parent::handleRecordCreation($data);
    }
}

