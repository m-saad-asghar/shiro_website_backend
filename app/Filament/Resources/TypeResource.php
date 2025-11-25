<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TypeResource\Pages;
use App\Models\Type;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Concerns\Translatable;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Section;

class TypeResource extends Resource
{
    use Translatable;

    protected static ?string $model = Type::class;
    protected static ?string $navigationIcon = 'heroicon-o-tag';
    protected static ?string $navigationGroup = 'Listings';
    protected static ?string $navigationLabel = 'Type';
    protected static ?string $pluralModelLabel = 'Types';
    protected static ?string $modelLabel = 'Type';
    protected static ?int $navigationSort = 3;

    public static function getTranslatableLocales(): array
    {
        return ['en', 'ar'];
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Type Details')
                ->schema([
                    Forms\Components\TextInput::make('name')->label('Name')->required()->maxLength(255),
                    Forms\Components\TextInput::make('prefix')->label('Reference Prefix')->helperText('Prefix for auto-generated Reference IDs (e.g., BYP, RTP).'),
                    Forms\Components\Toggle::make('for_agent')->label('Available for Agents')->required(),
                    Forms\Components\Toggle::make('for_developer')->label('Available for Developers')->required(),
                ])
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('name')->label('Name')->searchable(),
            Tables\Columns\TextColumn::make('prefix')->label('Prefix')->searchable(),
            Tables\Columns\IconColumn::make('for_agent')->label('For Agents')->boolean(),
            Tables\Columns\IconColumn::make('for_developer')->label('For Developers')->boolean(),
            Tables\Columns\TextColumn::make('created_at')->label('Created At')->dateTime()->sortable(),
            Tables\Columns\TextColumn::make('updated_at')->label('Updated At')->dateTime()->sortable(),
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
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTypes::route('/'),
            'create' => Pages\CreateType::route('/create'),
            'view' => Pages\ViewType::route('/{record}'),
            'edit' => Pages\EditType::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes([
            SoftDeletingScope::class,
        ]);
    }
}
