<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PropertyTypeResource\Pages;
use App\Models\PropertyType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Concerns\Translatable;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PropertyTypeResource extends Resource
{
    use Translatable;

    protected static ?string $model = PropertyType::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';
    protected static ?string $navigationGroup = 'Listings';
    protected static ?string $navigationLabel = 'Property Types';
    protected static ?string $pluralModelLabel = 'Property Types';
    protected static ?string $modelLabel = 'Property Type';
    protected static ?int    $navigationSort = 4;

    public static function getTranslatableLocales(): array
    {
        return ['en', 'ar'];
    }

    public static function getTranslatedNavigationLabel(): string
    {
        return __('Property Types');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')
                ->label(__('Name'))
                ->required()
                ->maxLength(255),

        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('Name'))
                    ->searchable(),



                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Created At'))
                    ->dateTime()
                    ->sortable(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('Updated At'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            // Define RelationManagers here if needed
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListPropertyTypes::route('/'),
            'create' => Pages\CreatePropertyType::route('/create'),
            'view'   => Pages\ViewPropertyType::route('/{record}'),
            'edit'   => Pages\EditPropertyType::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
