<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PrivacyResource\Pages;
use App\Models\Privacy;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Resources\Concerns\Translatable;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PrivacyResource extends Resource
{
    use Translatable;

    protected static ?string $model = Privacy::class;

    protected static ?string $navigationIcon = 'heroicon-o-lock-closed';
    protected static ?string $navigationGroup = 'Settings';
    protected static ?string $navigationLabel = 'Privacy Policy';
    protected static ?string $pluralModelLabel = 'Privacy Policies';
    protected static ?string $modelLabel = 'Privacy Policy';
    protected static ?int $navigationSort = 4;

    public static function getTranslatableLocales(): array
    {
        return ['en', 'ar'];
    }

    public static function form(Form $form): Form
    {
        return $form
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
            'index' => Pages\ListPrivacies::route('/'),
            'create' => Pages\CreatePrivacy::route('/create'),
            'view' => Pages\ViewPrivacy::route('/{record}'),
            'edit' => Pages\EditPrivacy::route('/{record}/edit'),
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
