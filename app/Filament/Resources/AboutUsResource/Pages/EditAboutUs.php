<?php

namespace App\Filament\Resources\AboutUsResource\Pages;

use App\Filament\Resources\AboutUsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAboutUs extends EditRecord
{
    use EditRecord\Concerns\Translatable;
    protected static string $resource = AboutUsResource::class;
    protected function getRedirectUrl() : string
    {
        return $this->getResource()::getUrl("index");
    }

    protected function getHeaderActions(): array
    {
        return [
             Actions\LocaleSwitcher::make(),
            Actions\ViewAction::make(),
//            Actions\DeleteAction::make(),
//            Actions\ForceDeleteAction::make(),
//            Actions\RestoreAction::make(),
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
