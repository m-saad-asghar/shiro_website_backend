<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServiceResource\Pages;
use App\Models\Service;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Resources\Concerns\Translatable;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Section;

class ServiceResource extends Resource
{
    use Translatable;

    protected static ?string $model = Service::class;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';
    protected static ?string $navigationGroup = 'Offerings';
    protected static ?string $navigationLabel = 'Service';
    protected static ?string $pluralModelLabel = 'Services';
    protected static ?string $modelLabel = 'Service';
    protected static ?int $navigationSort = 1;

    public static function getTranslatableLocales(): array
    {
        return ['en', 'ar'];
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Main Service')
                ->schema([
                    Forms\Components\TextInput::make('title_main')
                        ->label('Main Title')
                        ->required()
                        ->maxLength(255),

                    Forms\Components\FileUpload::make('image_main')
                        ->label('Main Image')
                        ->image(),

                    Forms\Components\Textarea::make('description')
                        ->label('Main Description')
                        ->columnSpanFull(),
                ])
                ->columns(2),

            Section::make('Sub Section')
                ->schema([
                    Forms\Components\FileUpload::make('sub_image')
                        ->label('Sub Image')
                        ->image(),

                    Forms\Components\TextInput::make('sub_title')
                        ->label('Sub Title')
                        ->maxLength(255),

                    Forms\Components\Textarea::make('description_header')
                        ->label('Header Description')
                        ->columnSpanFull(),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('title_main')->label('Main Title')->searchable(),
            Tables\Columns\ImageColumn::make('image_main')->label('Main Image'),
            Tables\Columns\ImageColumn::make('sub_image')->label('Sub Image'),
            Tables\Columns\TextColumn::make('sub_title')->label('Sub Title')->searchable(),
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
            'index' => Pages\ListServices::route('/'),
            'create' => Pages\CreateService::route('/create'),
            'view' => Pages\ViewService::route('/{record}'),
            'edit' => Pages\EditService::route('/{record}/edit'),
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
