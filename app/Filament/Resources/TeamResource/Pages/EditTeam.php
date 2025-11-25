<?php

namespace App\Filament\Resources\TeamResource\Pages;

use App\Filament\Resources\AgentResource;
use App\Filament\Resources\TeamResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTeam extends EditRecord
{
    use EditRecord\Concerns\Translatable;
    protected static string $resource = TeamResource::class;

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
