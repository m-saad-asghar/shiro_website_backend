<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TeamResource\Pages;
use App\Models\Team;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Concerns\Translatable;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TeamResource extends Resource
{
    use Translatable;

    protected static ?string $model = Team::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'Company Info';
    protected static ?string $navigationLabel = 'Team';
    protected static ?string $pluralModelLabel = 'Team Members';
    protected static ?string $modelLabel = 'Team Member';
    protected static ?int $navigationSort = 4;

    public static function getTranslatableLocales(): array
    {
        return ['en', 'ar'];
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Basic Info')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->required()
                        ->label('Name')
                        ->maxLength(255),

                    Forms\Components\Select::make('team_type')
                        ->label('Team Type')
                        ->options(Team::getTeamTypes())
                        ->default(Team::TYPE_BROKERS)
                        ->required()
                        ->helperText('Management for executives, Brokers for sales team'),

                    Forms\Components\TextInput::make('position')
                        ->label('Position')
                        ->maxLength(255),

                    Forms\Components\FileUpload::make('image')
                        ->label('Photo')
                        ->image()
                        ->columnSpanFull(),

                    Forms\Components\Textarea::make('bio')
                        ->label('Bio')
                        ->rows(5)
                        ->columnSpanFull(),
                ]),

            Forms\Components\Section::make('Contact')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('phone')
                        ->label('Phone')
                        ->tel()
                        ->maxLength(50),

                    Forms\Components\TextInput::make('whatsapp')
                        ->label('WhatsApp')
                        ->tel()
                        ->maxLength(50),
                ]),

            Forms\Components\Section::make('Social Media')
                ->description('Social media profiles and links')
                ->columns(3)
                ->schema([
                    Forms\Components\TextInput::make('facebook')
                        ->label('Facebook')
                        ->url()
                        ->placeholder('https://facebook.com/username')
                        ->maxLength(255),

                    Forms\Components\TextInput::make('linkedin')
                        ->label('LinkedIn')
                        ->url()
                        ->placeholder('https://linkedin.com/in/username')
                        ->maxLength(255),

                    Forms\Components\TextInput::make('instagram')
                        ->label('Instagram')
                        ->url()
                        ->placeholder('https://instagram.com/username')
                        ->maxLength(255),
                ]),

            Forms\Components\Section::make('Expertise')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('experience')
                        ->label('Experience')
                        ->helperText('مثال: "10 years" أو "10 سنوات"')
                        ->maxLength(100),

                    Forms\Components\TagsInput::make('languages')
                        ->label('Languages')
                        ->placeholder('Add languages...')
                        ->helperText('Type language and press Enter (e.g., English, Arabic, French)')
                        ->suggestions(['English', 'Arabic', 'French', 'Spanish', 'German', 'Russian', 'Chinese', 'Hindi', 'Urdu']),

                    Forms\Components\Textarea::make('areas_of_expertise')
                        ->label('Areas of Expertise')
                        ->helperText('أدخل قائمة مفصولة بفواصل، مثال: Al Furjan, Dubai Islands, Dubai Marina')
                        ->rows(3)
                        ->columnSpanFull(),

                    Forms\Components\Textarea::make('developers_of_expertise')
                        ->label('Developers of Expertise')
                        ->helperText('قائمة مفصولة بفواصل، مثال: Emaar Properties, Nakheel, Ellington Properties')
                        ->rows(3)
                        ->columnSpanFull(),
                ]),

            Forms\Components\Section::make('Meta')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('sort')
                        ->label('Sort')
                        ->numeric()
                        ->minValue(0)
                        ->default(0)
                        ->required(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('sort', 'asc')
            ->columns([
                Tables\Columns\ImageColumn::make('image')->label('Photo')->circular(),

                Tables\Columns\TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('team_type')
                    ->label('Type')
                    ->colors([
                        'primary' => 'management',
                        'success' => 'brokers',
                    ])
                    ->formatStateUsing(fn ($state) => ucfirst($state)),

                Tables\Columns\TextColumn::make('position')
                    ->label('Position')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('phone')
                    ->label('Phone')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('whatsapp')
                    ->label('WhatsApp')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('experience')
                    ->label('Experience')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('languages')
                    ->label('Languages')
                    ->limit(30)
                    ->formatStateUsing(fn ($record) => $record->formatted_languages)
                    ->tooltip(fn ($record) => $record->formatted_languages)
                    ->toggleable(),

                Tables\Columns\TextColumn::make('areas_of_expertise')
                    ->label('Areas')
                    ->limit(40)
                    ->tooltip(fn ($record) => $record->areas_of_expertise)
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('developers_of_expertise')
                    ->label('Developers')
                    ->limit(40)
                    ->tooltip(fn ($record) => $record->developers_of_expertise)
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('sort')
                    ->label('Sort')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('team_type')
                    ->label('Team Type')
                    ->options(Team::getTeamTypes())
                    ->placeholder('All Types'),
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

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListTeams::route('/'),
            'create' => Pages\CreateTeam::route('/create'),
            'view'   => Pages\ViewTeam::route('/{record}'),
            'edit'   => Pages\EditTeam::route('/{record}/edit'),
        ];
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes([
            SoftDeletingScope::class,
        ]);
    }
}
