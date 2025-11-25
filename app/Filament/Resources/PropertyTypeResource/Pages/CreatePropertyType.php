<?php

namespace App\Filament\Resources\PropertyTypeResource\Pages;

use App\Filament\Resources\PropertyTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePropertyType extends CreateRecord
{
    protected static string $resource = PropertyTypeResource::class;
    use CreateRecord\Concerns\Translatable;

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
}
