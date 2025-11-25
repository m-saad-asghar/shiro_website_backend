<?php

namespace App\Filament\Resources\ContactUsResource\Pages;

use App\Filament\Resources\ContactUsResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateContactUs extends CreateRecord
{
    use CreateRecord\Concerns\Translatable;
    protected static string $resource = ContactUsResource::class;
    protected static bool $canCreateAnother = false;

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
        $data = $this->mutateRepeaterArrayField(
            $data,
            'office',
            ['title', 'address', 'image', 'description', 'email', 'phone']
        );

        return $data;
    }

    /**
     * Helper for normalizing repeater fields before save.
     */
    protected function mutateRepeaterArrayField(array $data, string $field, array $keys): array
    {
        if (isset($data[$field]) && is_array($data[$field])) {
            $data[$field] = collect($data[$field])->map(function ($item) use ($keys) {
                $normalized = [];
                foreach ($keys as $key) {
                    $normalized[$key] = $item[$key] ?? ($key === 'image' ? null : '');
                }
                return $normalized;
            })->values()->toArray();
        }

        return $data;
    }
}
