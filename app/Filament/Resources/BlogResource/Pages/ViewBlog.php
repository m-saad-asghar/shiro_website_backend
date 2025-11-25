<?php

namespace App\Filament\Resources\BlogResource\Pages;

use App\Filament\Resources\BlogResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Grid;

class ViewBlog extends ViewRecord
{
    protected static string $resource = BlogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\LocaleSwitcher::make(),
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }

    public function getInfolist(string $name): ?Infolists\Infolist
    {
        return Infolists\Infolist::make()
            ->record($this->record)
            ->schema([

                // القسم الأول: معلومات عامة
                Section::make('Basic Information')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('title')
                                    ->label('Title')
                                    ->weight('bold')
                                    ->size('lg'),

                                TextEntry::make('slug')
                                    ->label('Slug')
                                    ->copyable(),

                                TextEntry::make('blogCategory.title')
                                    ->label('Category'),

                                TextEntry::make('is_active')
                                    ->label('Status')
                                    ->badge()
                                    ->color(fn ($state) => $state ? 'success' : 'warning')
                                    ->formatStateUsing(fn ($state) => $state ? 'Active' : 'Draft'),

                                TextEntry::make('created_at')
                                    ->label('Created At')
                                    ->dateTime(),

                                TextEntry::make('updated_at')
                                    ->label('Last Updated')
                                    ->dateTime(),
                            ]),
                    ]),

                // القسم الثاني: الصورة + المحتوى
                Section::make('Content')
                    ->schema([
                        ImageEntry::make('main_image')
                            ->label('Main Image')
                            ->columnSpanFull()
                            ->height('20rem')
                            ->extraImgAttributes(['style' => 'object-fit:cover;border-radius:12px']),

                        TextEntry::make('description')
                            ->label('Article Content')
                            ->html()
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
