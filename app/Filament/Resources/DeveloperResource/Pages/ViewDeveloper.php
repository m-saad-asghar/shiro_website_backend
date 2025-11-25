<?php

namespace App\Filament\Resources\DeveloperResource\Pages;

use App\Filament\Resources\DeveloperResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewDeveloper extends ViewRecord
{
    use ViewRecord\Concerns\Translatable;
    protected static string $resource = DeveloperResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\LocaleSwitcher::make(),
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
