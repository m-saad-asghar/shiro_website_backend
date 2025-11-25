<?php

namespace App\Filament\Resources\SaleAgentResource\Pages;

use App\Filament\Resources\RegionResource;
use App\Filament\Resources\SaleAgentResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateSaleAgent extends CreateRecord
{
//    use CreateRecord\Concerns\Translatable;
    protected static string $resource = SaleAgentResource::class;


    protected function getHeaderActions(): array
    {
        return [
//            Actions\LocaleSwitcher::make(),

        ];
    }


}
