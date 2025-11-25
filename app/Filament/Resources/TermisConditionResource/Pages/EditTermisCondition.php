<?php

namespace App\Filament\Resources\TermisConditionResource\Pages;

use App\Filament\Resources\TermisConditionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTermisCondition extends EditRecord
{
    use EditRecord\Concerns\Translatable;
    protected static string $resource = TermisConditionResource::class;

    protected function getHeaderActions(): array
    {
        return [
             Actions\LocaleSwitcher::make(),
            Actions\ViewAction::make(),
//            Actions\DeleteAction::make(),
//            Actions\ForceDeleteAction::make(),
//            Actions\RestoreAction::make(),
        ];
    }
    protected function getRedirectUrl() : string
    {
        return $this->getResource()::getUrl("index");
    }
}
