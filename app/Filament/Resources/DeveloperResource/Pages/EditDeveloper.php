<?php

namespace App\Filament\Resources\DeveloperResource\Pages;

use App\Filament\Resources\DeveloperResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDeveloper extends EditRecord
{
    use EditRecord\Concerns\Translatable;
    protected static string $resource = DeveloperResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (isset($data['contact_inf']) && is_array($data['contact_inf'])) {
            $data['contact_inf'] = collect($data['contact_inf'])->map(function ($item) {
                return [
                    'type' => $item['type'] ?? '',
                    'value' => $item['value'] ?? '',
                ];
            })->values()->toArray();
        }

        return $data;
    }


    protected function getHeaderActions(): array
    {
        return [
             Actions\LocaleSwitcher::make(),
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
    protected function getRedirectUrl() : string
    {
        return $this->getResource()::getUrl("index");
    }
}
