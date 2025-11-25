<?php

namespace App\Filament\Resources\CurrencyResource\Pages;

use App\Filament\Resources\CurrencyResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewCurrency extends ViewRecord
{
//    use ViewRecord\Concerns\Translatable;
    protected static string $resource = CurrencyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
//            Actions\LocaleSwitcher::make(),
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
