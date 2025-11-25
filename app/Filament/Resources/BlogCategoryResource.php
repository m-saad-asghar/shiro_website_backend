<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BlogCategoryResource\Pages;
use App\Models\BlogCategory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Concerns\Translatable;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BlogCategoryResource extends Resource
{
    use Translatable;

    protected static ?string $model = BlogCategory::class;

    protected static ?string $navigationIcon = 'heroicon-o-folder';
    protected static ?string $navigationGroup = 'Blog';
    protected static ?string $navigationLabel = 'Blog Categories';
    protected static ?string $pluralModelLabel = 'Blog Categories';
    protected static ?string $modelLabel = 'Blog Category';
    protected static ?int $navigationSort = 2;

    public static function getTranslatableLocales(): array
    {
        return ['en', 'ar'];
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                    Forms\Components\TextInput::make('title')
                        ->label('Category Name')
                        ->required()
                        ->maxLength(255),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Category Name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Updated At')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->label('View'),
                Tables\Actions\EditAction::make()->label('Edit'),
                Tables\Actions\DeleteAction::make()->label('Delete'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->label('Delete'),
                    Tables\Actions\ForceDeleteBulkAction::make()->label('Force Delete'),
                    Tables\Actions\RestoreBulkAction::make()->label('Restore'),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBlogCategories::route('/'),
            'create' => Pages\CreateBlogCategory::route('/create'),
            'view' => Pages\ViewBlogCategory::route('/{record}'),
            'edit' => Pages\EditBlogCategory::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes([
            SoftDeletingScope::class,
        ]);
    }
}
