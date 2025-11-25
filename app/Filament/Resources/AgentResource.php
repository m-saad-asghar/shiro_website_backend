<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AgentResource\Pages;
use App\Filament\Resources\AgentResource\RelationManagers\ContactAgentFormsRelationManager;
use App\Filament\Resources\AgentResource\RelationManagers\SalesRelationManager;
use App\Filament\Resources\DeveloperResource\RelationManagers\PropertiesRelationManager;
use App\Models\Agent;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Repeater;
use Filament\Resources\Concerns\Translatable;

class AgentResource extends Resource
{
    use Translatable;

    protected static ?string $model = Agent::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationGroup = 'Directory';
    protected static ?string $navigationLabel = 'Agent';
    protected static ?string $pluralModelLabel = 'Agents';
    protected static ?string $modelLabel = 'Agent';
    protected static ?int $navigationSort = 3;

    public static function getTranslatableLocales(): array
    {
        return ['en', 'ar'];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Agent Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Full Name')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('address')
                            ->label('Address')
                            ->maxLength(255),

                        Repeater::make('contact_inf')
                            ->label('Contact Info')
                            ->schema([
                                Forms\Components\TextInput::make('type')
                                    ->label('Type')
                                    ->placeholder('e.g. phone, whatsapp'),

                                Forms\Components\TextInput::make('value')
                                    ->label('Value')
                                    ->placeholder('e.g. +963 999 999'),
                            ])
                            ->addActionLabel('Add Contact Method')
                            ->collapsible()
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->maxLength(255),


                        Forms\Components\FileUpload::make('image')
                            ->label('Image')
                            ->image(),

                        Forms\Components\Textarea::make('description')
                            ->label('Description')
                            ->nullable()
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Name')
                    ->searchable(),

                Tables\Columns\TextColumn::make('address')
                    ->label('Address')
                    ->searchable(),

                Tables\Columns\TextColumn::make('contact_inf')
                    ->label('Contacts')
                    ->getStateUsing(fn ($record) => collect($record->contact_inf)->pluck('value')->implode(' | '))
                    ->wrap(),

                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Updated At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            SalesRelationManager::class,
            PropertiesRelationManager::class,
            ContactAgentFormsRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAgents::route('/'),
            'create' => Pages\CreateAgent::route('/create'),
            'view' => Pages\ViewAgent::route('/{record}'),
            'edit' => Pages\EditAgent::route('/{record}/edit'),
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
