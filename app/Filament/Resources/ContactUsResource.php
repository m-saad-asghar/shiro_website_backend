<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContactUsResource\Pages;
use App\Models\ContactUs;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Resources\Concerns\Translatable;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Section;

class ContactUsResource extends Resource
{
    use Translatable;

    protected static ?string $model = ContactUs::class;

    protected static ?string $navigationIcon = 'heroicon-o-phone';
    protected static ?string $navigationGroup = 'Messages';
    protected static ?string $navigationLabel = 'Contact Us';
    protected static ?string $pluralModelLabel = 'Contact Us Records';
    protected static ?string $modelLabel = 'Contact Us Record';
    protected static ?int $navigationSort = 1;

    public static function getTranslatableLocales(): array
    {
        return ['en', 'ar'];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Main Contact')
                    ->schema([
                        Forms\Components\TextInput::make('email')->label('Email'),
                        Forms\Components\TextInput::make('phone')->label('Primary Phone'),
                        Forms\Components\TextInput::make('secondary_phone')->label('Secondary Phone'),
                        Forms\Components\TextInput::make('whatsapp')->label('WhatsApp'),
                        Forms\Components\TextInput::make('fax')->label('Fax'),
                        Forms\Components\FileUpload::make('video')
                            ->label('Video'),
                        Forms\Components\TextInput::make('location')->label('Location'),
                        Forms\Components\TextInput::make('latitude')
                            ->label('Latitude')
                            ->numeric()
                            ->step(0.0000001), // 7 digits precision

                        Forms\Components\TextInput::make('longitude')
                            ->label('Longitude')
                            ->numeric()
                            ->step(0.0000001),
                        Forms\Components\Textarea::make('map_iframe')->label('Google Map Iframe')->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Social Media')
                    ->schema([
                        Forms\Components\TextInput::make('facebook')->label('Facebook'),
                        Forms\Components\TextInput::make('instagram')->label('Instagram'),
                        Forms\Components\TextInput::make('twitter')->label('Twitter'),
                        Forms\Components\TextInput::make('linkedin')->label('LinkedIn'),
                        Forms\Components\TextInput::make('tiktok')->label('TikTok'),
                        Forms\Components\TextInput::make('work_hours')->label('Working Hours'),
                    ])
                    ->columns(2),

                Section::make('Offices')
                    ->description('Manage your offices with details')
                    ->schema([
                        Forms\Components\Repeater::make('office')
                            ->label('Offices')
                            ->schema([
                                Forms\Components\TextInput::make('title')->label('Title')->required(),
                                Forms\Components\TextInput::make('address')->label('Address')->required(),
                                Forms\Components\FileUpload::make('image')->label('Image')->image(),
                                Forms\Components\Textarea::make('description')->label('Description'),
                                Forms\Components\TextInput::make('email')->label('Email'),
                                Forms\Components\TextInput::make('phone')->label('Phone'),
                            ])
                            ->collapsible()
                            ->orderable()
                            ->default([])
                            ->addActionLabel('Add Office'),
                    ])
                    ->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('email')->label('Email')->searchable(),
                Tables\Columns\TextColumn::make('phone')->label('Phone')->searchable(),
                Tables\Columns\TextColumn::make('whatsapp')->label('WhatsApp')->searchable(),
                Tables\Columns\TextColumn::make('location')->label('Location')->searchable(),
                Tables\Columns\TextColumn::make('latitude')->label('Latitude')->sortable(),
                Tables\Columns\TextColumn::make('longitude')->label('Longitude')->sortable(),
                Tables\Columns\TextColumn::make('work_hours')->label('Working Hours'),
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
            'index' => Pages\ListContactUs::route('/'),
            'create' => Pages\CreateContactUs::route('/create'),
            'view' => Pages\ViewContactUs::route('/{record}'),
            'edit' => Pages\EditContactUs::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes([SoftDeletingScope::class]);
    }
}
