<?php

namespace App\Filament\Resources\SaleAgentResource\Pages;

use App\Filament\Resources\SaleAgentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSaleAgent extends EditRecord
{
    protected static string $resource = SaleAgentResource::class;

//    use EditRecord\Concerns\Translatable;

    protected function getHeaderActions(): array
    {
        return [
//            Actions\LocaleSwitcher::make(),
            Actions\ViewAction::make(),
//            Actions\DeleteAction::make(),
//            Actions\ForceDeleteAction::make(),
//            Actions\RestoreAction::make(),
        ];
    }



}
