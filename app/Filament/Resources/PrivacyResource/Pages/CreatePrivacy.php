<?php

namespace App\Filament\Resources\PrivacyResource\Pages;

use App\Filament\Resources\PrivacyResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePrivacy extends CreateRecord
{
    use CreateRecord\Concerns\Translatable;
    protected static string $resource = PrivacyResource::class;
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
}
