<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SliderResource\Pages;
use App\Models\Slider;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Resources\Concerns\Translatable;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Section;

class SliderResource extends Resource
{
    use Translatable;

    protected static ?string $model = Slider::class;

    protected static ?string $navigationIcon = 'heroicon-o-photo';
    protected static ?string $navigationGroup = 'Settings';
    protected static ?string $navigationLabel = 'Slider';
    protected static ?string $pluralModelLabel = 'Sliders';
    protected static ?string $modelLabel = 'Slider';
    protected static ?int $navigationSort = 5;

    public static function getTranslatableLocales(): array
    {
        return ['en', 'ar'];
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Slider Info')
                ->schema([
                    Forms\Components\Select::make('page')
                        ->label('Page')
                        ->options([
                            'home' => 'Home',
                        ])
                        ->required()
                        ->native(false),

                    Forms\Components\FileUpload::make('image')
                        ->label('Slider Image')
                        ->image()
                        ->directory('sliders'),

                    Forms\Components\TextInput::make('title')
                        ->label('Title')
                        ->maxLength(255),

                    Forms\Components\FileUpload::make('video')
                        ->label('Video'),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('page')->label('Page')->searchable(),
            Tables\Columns\ImageColumn::make('image')->label('Image'),
            Tables\Columns\TextColumn::make('title')->label('Title')->searchable(),
            Tables\Columns\TextColumn::make('video')->label('Video')->searchable(),
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
            'index' => Pages\ListSliders::route('/'),
            'create' => Pages\CreateSlider::route('/create'),
            'view' => Pages\ViewSlider::route('/{record}'),
            'edit' => Pages\EditSlider::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes([
            SoftDeletingScope::class,
        ]);
    }
}
