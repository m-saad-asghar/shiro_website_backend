<?php

namespace App\Filament\Resources\BlogCategoryResource\Pages;

use App\Filament\Resources\BlogCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateBlogCategory extends CreateRecord
{
    protected static string $resource = BlogCategoryResource::class;
    use CreateRecord\Concerns\Translatable;
    protected function getRedirectUrl() : string
    {
        return $this->getResource()::getUrl("index");
    }
    protected function getHeaderActions(): array
    {
        return [
            Actions\LocaleSwitcher::make(),

        ];
    }

}
