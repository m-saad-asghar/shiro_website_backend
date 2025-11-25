<?php

namespace App\Filament\Resources\SaleAgentResource\Pages;

use App\Filament\Resources\SaleAgentResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewSaleAgent extends ViewRecord
{
    protected static string $resource = SaleAgentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
