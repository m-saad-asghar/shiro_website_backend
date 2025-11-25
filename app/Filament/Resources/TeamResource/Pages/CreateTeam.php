<?php

namespace App\Filament\Resources\TeamResource\Pages;

use App\Filament\Resources\AgentResource;
use App\Filament\Resources\TeamResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateTeam extends CreateRecord
{
    use CreateRecord\Concerns\Translatable;
    protected static string $resource = TeamResource::class;
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
