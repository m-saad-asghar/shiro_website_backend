<?php

namespace App\Filament\Resources\PrivacyResource\Pages;

use App\Filament\Resources\PrivacyResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPrivacy extends ViewRecord
{
    use ViewRecord\Concerns\Translatable;
    protected static string $resource = PrivacyResource::class;

    protected function getHeaderActions(): array
    {
        return [
             Actions\LocaleSwitcher::make(),
            Actions\EditAction::make(),
        ];
    }
}
