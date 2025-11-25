<?php

namespace App\Filament\Resources\SliderResource\Pages;

use App\Filament\Resources\SliderResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateSlider extends CreateRecord
{
    use CreateRecord\Concerns\Translatable;
    protected static string $resource = SliderResource::class;
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
