<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AboutUsResource\Pages;
use App\Models\AboutUs;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Concerns\Translatable;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Section;

class AboutUsResource extends Resource
{
    use Translatable;

    protected static ?string $model = AboutUs::class;

    protected static ?string $navigationIcon = 'heroicon-o-information-circle';
    protected static ?string $navigationGroup = 'Settings';
    protected static ?string $navigationLabel = 'About Section';
    protected static ?string $pluralModelLabel = 'About Sections';
    protected static ?string $modelLabel = 'About Section';
    protected static ?int $navigationSort = 2;

    public static function getTranslatableLocales(): array
    {
        return ['en', 'ar'];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Manager Info')
                    ->schema([
                        Forms\Components\TextInput::make('manager_name')->label('Manager Name'),
                        Forms\Components\TextInput::make('manager_position')->label('Manager Position'),
                        Forms\Components\FileUpload::make('manager_image')->label('Manager Image')->image(),
                        Forms\Components\Textarea::make('manager_description')->label('Manager Description'),
                    ])
                    ->columns(2),

                Section::make('Media')
                    ->schema([
                        Forms\Components\FileUpload::make('video_url')->label('Video'),
                    ])
                    ->columns(2),

                Section::make('Main Info')
                    ->schema([
                        Forms\Components\TextInput::make('title')->label('Title'),
                        Forms\Components\Textarea::make('sub_description')->label('Sub Description'),
                        Forms\Components\RichEditor::make('description')->label('Description'),
                        Forms\Components\RichEditor::make('content')->label('Content'),
                    ])
                    ->columns(2),

                Section::make('Details')
                    ->schema([
                        Forms\Components\Textarea::make('vision')->label('Vision'),
                        Forms\Components\Textarea::make('mission')->label('Mission'),
                        Forms\Components\Textarea::make('apart')->label('Apart'),
                        Forms\Components\Textarea::make('approach')->label('Approach'),
                        Forms\Components\Textarea::make('philosophy')->label('Philosophy'),
                    ])
                    ->columns(2),

                Section::make('Lists')
                    ->schema([
                        Forms\Components\Repeater::make('Our_value')
                            ->label('Our Values')
                            ->schema([
                                Forms\Components\TextInput::make('title')->label('Title'),
                                Forms\Components\Textarea::make('description')->label('Description'),
                            ])
                            ->defaultItems(1)
                            ->reorderable()
                            ->columns(1),

                        Forms\Components\Repeater::make('target')
                            ->label('Our Targets')
                            ->schema([
                                Forms\Components\TextInput::make('title')->label('Title'),
                                Forms\Components\Textarea::make('description')->label('Description'),
                            ])
                            ->defaultItems(1)
                            ->reorderable()
                            ->columns(1),
                    ])
                    ->columns(2),

                Section::make('Partners & Services')
                    ->schema([
                        Forms\Components\Textarea::make('text_partner')->label('Text Partner'),
                        Forms\Components\Textarea::make('text_services')->label('Text Services'),
                    ])
                    ->columns(2),
            ]);

    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('manager_name')
                    ->label('Manager Name')
                    ->searchable(),

                Tables\Columns\TextColumn::make('manager_position')
                    ->label('Manager Position')
                    ->searchable(),

                Tables\Columns\ImageColumn::make('manager_image')
                    ->label('Manager Image'),

                Tables\Columns\TextColumn::make('video_url')
                    ->label('Video URL')
                    ->searchable(),



                Tables\Columns\TextColumn::make('deleted_at')
                    ->label('Deleted At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

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
                Tables\Actions\BulkActionGroup::make([]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAboutUs::route('/'),
            'create' => Pages\CreateAboutUs::route('/create'),
            'view' => Pages\ViewAboutUs::route('/{record}'),
            'edit' => Pages\EditAboutUs::route('/{record}/edit'),
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
