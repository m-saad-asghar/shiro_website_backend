<?php

namespace App\Filament\Resources\AboutUsResource\Pages;

use App\Filament\Resources\AboutUsResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateAboutUs extends CreateRecord
{
    use CreateRecord\Concerns\Translatable;
    protected static string $resource = AboutUsResource::class;
    protected static bool $canCreateAnother = false;
    protected function getRedirectUrl() : string
    {
        return $this->getResource()::getUrl("index");
    }
    protected function getHeaderActions(): array
    {
        return [
             Actions\LocaleSwitcher::make(),

        ];
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $arrayFields = [
            'Our_value',
            'target',
        ];

        foreach ($arrayFields as $field) {
            if (isset($data[$field]) && is_array($data[$field])) {
                $data[$field] = collect($data[$field])->map(function ($item) {
                    return [
                        'title' => $item['title'] ?? '',
                        'description' => $item['description'] ?? '',
                    ];
                })->values()->toArray();
            }
        }

        return $data;
    }
}
