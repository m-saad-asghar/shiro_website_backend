<?php

namespace App\Filament\Resources\SaleAgentResource\Pages;

use App\Filament\Resources\SaleAgentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSaleAgents extends ListRecords
{
    protected static string $resource = SaleAgentResource::class;
//    use ListRecords\Concerns\Translatable;

    protected function getHeaderActions(): array
    {
        return [
//            Actions\LocaleSwitcher::make(),
            Actions\CreateAction::make(),
        ];
    }
}
