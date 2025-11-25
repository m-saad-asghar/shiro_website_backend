<?php

namespace App\Filament\Resources\SubscribeResource\Pages;

use App\Filament\Resources\SubscribeResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewSubscribe extends ViewRecord
{
    protected static string $resource = SubscribeResource::class;

    protected function getHeaderActions(): array
    {
        return [
//            Actions\EditAction::make(),
        ];
    }
}
