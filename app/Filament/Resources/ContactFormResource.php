<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContactFormResource\Pages;
use App\Models\ContactForm;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ContactFormResource extends Resource
{
    protected static ?string $model = ContactForm::class;

    protected static ?string $navigationIcon = 'heroicon-o-envelope';
    protected static ?string $navigationGroup = 'Messages';
    protected static ?string $navigationLabel = 'Contact Form';
    protected static ?string $pluralModelLabel = 'Contact Forms';
    protected static ?string $modelLabel = 'Contact Form';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Name')
                    ->disabled(),

                Forms\Components\TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->disabled(),

                Forms\Components\TextInput::make('phone')
                    ->label('Phone')
                    ->tel()
                    ->disabled(),

                Forms\Components\Textarea::make('message')
                    ->label('Message')
                    ->disabled()
                    ->columnSpanFull(),

                Forms\Components\TextInput::make('language')
                    ->label('Language')
                    ->disabled(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Name')->searchable(),
                Tables\Columns\TextColumn::make('email')->label('Email')->searchable(),
                Tables\Columns\TextColumn::make('phone')->label('Phone')->searchable(),
                Tables\Columns\TextColumn::make('language')->label('Language')->searchable(),
                Tables\Columns\TextColumn::make('created_at')->label('Created At')->dateTime()->sortable(),
                Tables\Columns\TextColumn::make('updated_at')->label('Updated At')->dateTime()->sortable(),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([]); // منع الحذف الجماعي
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListContactForms::route('/'),
            'view' => Pages\ViewContactForm::route('/{record}'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes([SoftDeletingScope::class]);
    }
}
