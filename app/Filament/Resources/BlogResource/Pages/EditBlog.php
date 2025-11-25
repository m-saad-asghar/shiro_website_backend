<?php

namespace App\Filament\Resources\BlogResource\Pages;

use App\Filament\Resources\BlogResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBlog extends EditRecord
{
    protected static string $resource = BlogResource::class;
    use EditRecord\Concerns\Translatable;

    protected function getRedirectUrl() : string
    {
        return $this->getResource()::getUrl("index");
    }
    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\LocaleSwitcher::make(),
            Actions\RestoreAction::make(),

        ];
    }
}
