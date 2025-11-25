<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DeveloperResource\Pages;
use App\Filament\Resources\DeveloperResource\RelationManagers\PropertiesRelationManager;
use App\Models\Developer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Resources\Concerns\Translatable;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;

class DeveloperResource extends Resource
{
    use Translatable;

    protected static ?string $model = Developer::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';
    protected static ?string $navigationGroup = 'Directory';
    protected static ?string $navigationLabel = 'Developer';
    protected static ?string $pluralModelLabel = 'Developers';
    protected static ?string $modelLabel = 'Developer';
    protected static ?int $navigationSort = 3;

    public static function getTranslatableLocales(): array
    {
        return ['en', 'ar'];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Developer Info')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Developer Name')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->maxLength(255),

                        Repeater::make('contact_inf')
                            ->label('Contact Info')
                            ->schema([
                                Forms\Components\TextInput::make('type')->label('Type'),
                                Forms\Components\TextInput::make('value')->label('Value'),
                            ])
                            ->addActionLabel('Add Contact')
                            ->collapsible()
                            ->columnSpanFull(),

                        Forms\Components\FileUpload::make('logo')
                            ->label('Logo')
                            ->image(),
                        Forms\Components\Textarea::make('description')
                            ->label('Description')
                            ->nullable()
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('description_top')
                            ->label('Top Description')
                            ->nullable()
                            ->columnSpanFull(),

                        // ✅ الحقل الحالي
                        Forms\Components\Textarea::make('description_bottom')
                            ->label('Bottom Description')
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
                Tables\Columns\TextColumn::make('name')->label('Developer Name')->searchable(),
                Tables\Columns\TextColumn::make('email')->label('Email')->searchable(),
                Tables\Columns\TextColumn::make('contact_inf')
                    ->label('Contact Info')
                    ->getStateUsing(fn($record) => collect($record->contact_inf)->pluck('value')->implode(' | '))
                    ->wrap(),
                Tables\Columns\ImageColumn::make('logo')->label('Logo'),
                Tables\Columns\TextColumn::make('properties_count') // عمود عدد العقارات
                ->label('Number of Properties')
                    ->counts('properties') // يعد عدد السجلات في علاقة properties
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')->label('Created At')->dateTime()->sortable(),
                Tables\Columns\TextColumn::make('updated_at')->label('Updated At')->dateTime()->sortable(),
            ])
            ->filters([
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('from')->label('From Date'),
                        Forms\Components\DatePicker::make('to')->label('To Date'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        return $query
                            ->when($data['from'] ?? null, fn($q, $date) => $q->whereDate('created_at', '>=', $date))
                            ->when($data['to'] ?? null, fn($q, $date) => $q->whereDate('created_at', '<=', $date));
                    }),
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
            PropertiesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDevelopers::route('/'),
            'create' => Pages\CreateDeveloper::route('/create'),
            'view' => Pages\ViewDeveloper::route('/{record}'),
            'edit' => Pages\EditDeveloper::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes([
            SoftDeletingScope::class,
        ]);
    }

}
