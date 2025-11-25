<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReviewResource\Pages;
use App\Models\Review;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Resources\Concerns\Translatable;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Section;

class ReviewResource extends Resource
{
    use Translatable;

    protected static ?string $model = Review::class;

    protected static ?string $navigationIcon = 'heroicon-o-star';
    protected static ?string $navigationGroup = 'User Content';
    protected static ?string $navigationLabel = 'Review';
    protected static ?string $pluralModelLabel = 'Reviews';
    protected static ?string $modelLabel = 'Review';
    protected static ?int $navigationSort = 1;

    public static function getTranslatableLocales(): array
    {
        return ['en', 'ar'];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Review Info')
                    ->schema([
                        Forms\Components\FileUpload::make('image')
                            ->label('Reviewer Image')
                            ->image(),

                        Forms\Components\TextInput::make('name')
                            ->label('Reviewer Name')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('rate')
                            ->label('Rating')
                            ->required()
                            ->numeric()
                            ->step(0.1)
                            ->minValue(1)
                            ->maxValue(5)
                            ->default(5.0),

                        Forms\Components\TextInput::make('title')
                            ->label('Review Title')
                            ->maxLength(255),

                        Forms\Components\Textarea::make('description')
                            ->label('Review Description')
                            ->columnSpanFull(),

                        Forms\Components\DatePicker::make('date')
                            ->label('Review Date')
                            ->maxDate(now())
                            ->helperText('Cannot select a future date')
                            ->required(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')->label('Image'),
                Tables\Columns\TextColumn::make('name')->label('Name')->searchable(),
                Tables\Columns\TextColumn::make('rate')->label('Rate')->sortable(),
                Tables\Columns\TextColumn::make('title')->label('Title')->searchable(),
                Tables\Columns\TextColumn::make('date')->label('Date')->date()->sortable(),
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
            'index' => Pages\ListReviews::route('/'),
            'create' => Pages\CreateReview::route('/create'),
            'view' => Pages\ViewReview::route('/{record}'),
            'edit' => Pages\EditReview::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes([
            SoftDeletingScope::class,
        ]);
    }
}
