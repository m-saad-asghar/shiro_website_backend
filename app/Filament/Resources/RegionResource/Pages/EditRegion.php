<?php

namespace App\Filament\Resources\RegionResource\Pages;

use App\Filament\Resources\RegionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRegion extends EditRecord
{
    use EditRecord\Concerns\Translatable;
    protected static string $resource = RegionResource::class;

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
