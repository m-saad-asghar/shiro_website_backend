<?php

namespace App\Filament\Resources\PropertyTypeResource\Pages;

use App\Filament\Resources\PropertyTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPropertyTypes extends ListRecords
{
    protected static string $resource = PropertyTypeResource::class;
    use ListRecords\Concerns\Translatable;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
