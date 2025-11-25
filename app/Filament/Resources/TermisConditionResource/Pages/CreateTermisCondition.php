<?php

namespace App\Filament\Resources\TermisConditionResource\Pages;

use App\Filament\Resources\TermisConditionResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateTermisCondition extends CreateRecord
{
    use CreateRecord\Concerns\Translatable;
    protected static string $resource = TermisConditionResource::class;
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
