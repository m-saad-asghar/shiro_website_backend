<?php

namespace App\Filament\Resources\TermisConditionResource\Pages;

use App\Filament\Resources\TermisConditionResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewTermisCondition extends ViewRecord
{
    use ViewRecord\Concerns\Translatable;
    protected static string $resource = TermisConditionResource::class;

    protected function getHeaderActions(): array
    {
        return [
             Actions\LocaleSwitcher::make(),
            Actions\EditAction::make(),
        ];
    }
}
