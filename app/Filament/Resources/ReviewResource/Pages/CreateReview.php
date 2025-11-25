<?php

namespace App\Filament\Resources\ReviewResource\Pages;

use App\Filament\Resources\ReviewResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateReview extends CreateRecord
{
    use CreateRecord\Concerns\Translatable;
    protected static string $resource = ReviewResource::class;
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
