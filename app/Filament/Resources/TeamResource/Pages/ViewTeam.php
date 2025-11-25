<?php

namespace App\Filament\Resources\TeamResource\Pages;

use App\Filament\Resources\AgentResource;
use App\Filament\Resources\TeamResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewTeam extends ViewRecord
{
    use ViewRecord\Concerns\Translatable;
    protected static string $resource = TeamResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\LocaleSwitcher::make(),
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
