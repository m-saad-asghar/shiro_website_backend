<?php

namespace App\Filament\Resources\TypeResource\Pages;

use App\Filament\Resources\TypeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTypes extends ListRecords
{
    use ListRecords\Concerns\Translatable;
    protected static string $resource = TypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
             Actions\LocaleSwitcher::make(),
            Actions\CreateAction::make(),
        ];
    }
}
