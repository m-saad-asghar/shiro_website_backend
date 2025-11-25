<?php

namespace App\Filament\Resources\ContactUsResource\Pages;

use App\Filament\Resources\ContactUsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListContactUs extends ListRecords
{
    use ListRecords\Concerns\Translatable;
    protected static string $resource = ContactUsResource::class;

    protected function getHeaderActions(): array
    {
        return [
             Actions\LocaleSwitcher::make(),
            Actions\CreateAction::make(),
        ];
    }

    public function mount(): void
    {
        parent::mount();

        // Check if there is only one record
        $record = static::getResource()::getModel()::query()->first();
        $count = static::getResource()::getModel()::count();

        if ($count === 1 && $record) {
            // Redirect to the view page for the single record
            $this->redirect(static::getResource()::getUrl('view', ['record' => $record->getKey()]));
        }
    }

}
