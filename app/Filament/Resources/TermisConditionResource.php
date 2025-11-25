<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TermisConditionResource\Pages;
use App\Models\TermisCondition;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Resources\Concerns\Translatable;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Section;

class TermisConditionResource extends Resource
{
    use Translatable;

    protected static ?string $model = TermisCondition::class;

    protected static ?string $navigationIcon = 'heroicon-o-scale';
    protected static ?string $navigationGroup = 'Settings';
    protected static ?string $navigationLabel = 'Terms & Conditions';
    protected static ?string $pluralModelLabel = 'Terms & Conditions';
    protected static ?string $modelLabel = 'Terms & Conditions';
    protected static ?int $navigationSort = 6;

    public static function getTranslatableLocales(): array
    {
        return ['en', 'ar'];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Terms Info')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Title')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\RichEditor::make('description')
                            ->toolbarButtons([
                                'bold',
                                'h2',
                                'h3',
                                'italic',
                            ])
                            ->label('Content')
                            ->required()
                            ->columnSpanFull(),
                    ])
                    ->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Title')->searchable(),
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
//                    Tables\Actions\DeleteBulkAction::make(),
//                    Tables\Actions\ForceDeleteBulkAction::make(),
//                    Tables\Actions\RestoreBulkAction::make(),
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
            'index' => Pages\ListTermisConditions::route('/'),
            'create' => Pages\CreateTermisCondition::route('/create'),
            'view' => Pages\ViewTermisCondition::route('/{record}'),
            'edit' => Pages\EditTermisCondition::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes([
            SoftDeletingScope::class,
        ]);
    }
}
