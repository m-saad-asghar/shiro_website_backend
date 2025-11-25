<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PropertyResource\Pages;
use App\Models\Property;
use App\Models\Type;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Resources\Concerns\Translatable;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Section;

class PropertyResource extends Resource
{
    use Translatable;

    protected static ?string $model = Property::class;
    protected static ?string $navigationIcon = 'heroicon-o-home-modern';
    protected static ?string $navigationGroup = 'Listings';
    protected static ?string $navigationLabel = 'Property';
    protected static ?string $pluralModelLabel = 'Properties';
    protected static ?int $navigationSort = 1;

    public static function getTranslatableLocales(): array
    {
        return ['en', 'ar'];
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Basic Info')
                ->schema([
                    Forms\Components\TextInput::make('title')->label('Title')->required(),
                    Forms\Components\TextInput::make('meta_title')
                        ->label('Meta Title')
                        ->required()
                        ->maxLength(60)
                        ->helperText('SEO meta title (recommended: 50-60 characters)')
                        ->columnSpanFull(),
                    Forms\Components\Textarea::make('meta_description')
                        ->label('Meta Description')
                        ->required()
                        ->maxLength(160)
                        ->rows(3)
                        ->helperText('SEO meta description (recommended: 150-160 characters)')
                        ->columnSpanFull(),
                    Forms\Components\TextInput::make('slug')->label('Slug')->disabled()->helperText('Generated automatically from title'),
                    Forms\Components\Select::make('type_id')
                        ->label('Type')
                        ->relationship('type', 'name')
                        ->searchable()
                        ->preload()
                        ->required()
                        ->reactive()
                        ->afterStateUpdated(fn (callable $set) => [
                            $set('agent_id', null),
                            $set('developer_id', null),
                        ]),
                    Forms\Components\Select::make('property_type_id')
                        ->label('Property Type')
                        ->relationship('propertyType', 'name')
                        ->searchable()
                        ->preload()
                        ->required(),
                    Forms\Components\Textarea::make('purpose')->label('Purpose'),
                    Forms\Components\Toggle::make('is_finish')->label('Is Finished'),
                    Forms\Components\Textarea::make('completion')->label('Completion')->columnSpanFull(),
                ])->columns(2),

            Section::make('Descriptions & Profile')
                ->schema([
                    Forms\Components\Textarea::make('description')->label('Description')->columnSpanFull(),
                    Forms\Components\Textarea::make('profile')->label('Profile')->columnSpanFull(),
                ]),

            Section::make('Pricing & Location')
                ->schema([
                    Forms\Components\TextInput::make('price')
                        ->label('Price')
                        ->numeric()
                        ->prefix('د.إ')
                        ->maxValue(9999999999.99)
                        ->reactive()
                        ->afterStateUpdated(function ($state, callable $set, callable $get) {
                            if ($state !== null && $state !== '') {
                                $valueStr = number_format((float)$state, 2, '.', '');
                                $integerPart = explode('.', $valueStr)[0];
                                if (strlen($integerPart) > 10) {
                                    $set('price', null);
                                    \Filament\Notifications\Notification::make()
                                        ->title('Invalid Price')
                                        ->body('The price must not exceed 10 digits before the decimal point (maximum: 9999999999.99).')
                                        ->danger()
                                        ->send();
                                }
                            }
                        })
                        ->helperText('Maximum 10 digits before decimal point (e.g., 9999999999.99)'),
                    Forms\Components\TextInput::make('rental_period')->label('Rental Period'),
                    Forms\Components\TextInput::make('location')->label('Location'),
                    Forms\Components\TextInput::make('area')->label('Area')->numeric(),
                    Forms\Components\Select::make('region_id')
                        ->label('Region')
                        ->relationship('region', 'name')
                        ->searchable()
                        ->preload(),
                    Forms\Components\TextInput::make('zone_name')->label('Zone Name'),
                ])->columns(2),

            Section::make('Map Location')
                ->description('Set the property location on the map')
                ->schema([
                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\TextInput::make('latitude')
                                ->label('Latitude')
                                ->numeric()
                                ->step(0.00000001)
                                ->placeholder('25.276987')
                                ->helperText('Latitude coordinate (e.g., 25.276987)'),
                            Forms\Components\TextInput::make('longitude')
                                ->label('Longitude')
                                ->numeric()
                                ->step(0.00000001)
                                ->placeholder('55.296249')
                                ->helperText('Longitude coordinate (e.g., 55.296249)'),
                        ]),
                    Forms\Components\Textarea::make('map_address')
                        ->label('Full Address from Map')
                        ->placeholder('Full address as shown on the map')
                        ->helperText('Complete address including street, area, city')
                        ->columnSpanFull(),
                    Forms\Components\TextInput::make('map_embed_url')
                        ->label('Google Maps Embed URL')
                        ->placeholder('https://www.google.com/maps/embed?pb=...')
                        ->helperText('Optional: Google Maps embed URL for iframe')
                        ->columnSpanFull(),
                    Forms\Components\Placeholder::make('map_instructions')
                        ->label('How to get coordinates:')
                        ->content('
                            1. Go to Google Maps
                            2. Right-click on the property location
                            3. Click on the coordinates to copy them
                            4. Paste latitude and longitude in the fields above
                        ')
                        ->columnSpanFull(),
                ])->collapsible(),

            Section::make('Off-Plan Specific')
                ->description('Additional fields for off-plan properties (all optional)')
                ->schema([
                    Forms\Components\TextInput::make('starting_price')
                        ->label('Starting Price')
                        ->numeric()
                        ->prefix('د.إ')
                        ->maxValue(9999999999.99)
                        ->reactive()
                        ->afterStateUpdated(function ($state, callable $set, callable $get) {
                            if ($state !== null && $state !== '') {
                                $valueStr = number_format((float)$state, 2, '.', '');
                                $integerPart = explode('.', $valueStr)[0];
                                if (strlen($integerPart) > 10) {
                                    $set('starting_price', null);
                                    \Filament\Notifications\Notification::make()
                                        ->title('Invalid Starting Price')
                                        ->body('The starting price must not exceed 10 digits before the decimal point (maximum: 9999999999.99).')
                                        ->danger()
                                        ->send();
                                }
                            }
                        })
                        ->helperText('Starting price for off-plan units (optional). Maximum 10 digits before decimal point.'),
                    Forms\Components\TextInput::make('handover_year')
                        ->label('Handover Year')
                        ->numeric()
                        ->minValue(2024)
                        ->maxValue(2035)
                        ->helperText('Expected handover year (optional)'),
                    Forms\Components\TextInput::make('payment_plan')
                        ->label('Payment Plan')
                        ->placeholder('e.g., 60/40, 70/30')
                        ->helperText('Payment structure (optional)'),
                    Forms\Components\Textarea::make('property_mix')
                        ->label('Property Mix')
                        ->placeholder('e.g., 1 & 2 BR + Offices')
                        ->helperText('Types of units available (optional)')
                        ->columnSpanFull(),
                ])->columns(2),

            Section::make('Details & Ownership')
                ->schema([
                    Forms\Components\TextInput::make('num_bathroom')->label('Bathrooms')->required()->numeric(),
                    Forms\Components\TextInput::make('num_bedroom')->label('Bedrooms')->required()->numeric(),
                    Forms\Components\Select::make('agent_id')
                        ->label('Agent')
                        ->relationship('agent', 'name')
                        ->searchable()
                        ->preload()
                        ->visible(fn ($get) => optional(Type::find($get('type_id')))->for_agent),
                    Forms\Components\Select::make('developer_id')
                        ->label('Developer')
                        ->relationship('developer', 'name')
                        ->searchable()
                        ->preload()
                        ->visible(fn ($get) => optional(Type::find($get('type_id')))->for_developer),
                    Forms\Components\Select::make('service_id')
                        ->label('Service')
                        ->relationship('service', 'title_main')
                        ->searchable()
                        ->preload(),
                ])->columns(2),

            Section::make('Regulatory Information')
                ->description('DLD permits, broker license, agent license, QR code, and reference ID for this property.')
                ->schema([

                    Forms\Components\TextInput::make('broker_license')->label('Broker License'),
                    Forms\Components\TextInput::make('dld_permit_number')->label('DLD Permit Number'),
                    Forms\Components\TextInput::make('agent_license')->label('Agent License'),
                    Forms\Components\FileUpload::make('qr_code')->label('QR Code URL')->image(),
                    Forms\Components\TextInput::make('reference_id')->label('Reference ID')->disabled(),
                    Forms\Components\TextInput::make('dubailand_link')->label('Dubailand Link'),
                ])->columns(2),

            Section::make('Sale Status')
                ->schema([
                    Forms\Components\Toggle::make('is_sale')->label('For Sale')->reactive(),
                    Forms\Components\Toggle::make('is_home')->label('Featured on Home'),
                    Forms\Components\DatePicker::make('date_sale')->label('Sale Date')->visible(fn ($get) => $get('is_sale')),
                ])->columns(3),

            Section::make('Images')
                ->schema([
                    Forms\Components\FileUpload::make('images')
                        ->label('Images')
                        ->image()
                        ->multiple()
                        ->reorderable()
                        ->required(fn ($get) => $get('is_sale'))
                        ->minFiles(fn ($get) => $get('is_sale') ? 3 : 0)
                        ->helperText('At least 3 images are required when property is for sale. You can drag and drop to reorder them.'),
                ]),

            Section::make('Documents')
                ->schema([
                    Forms\Components\FileUpload::make('offplan_pdf')
                        ->label('Off-Plan PDF')
                        ->acceptedFileTypes(['application/pdf'])
                        ->directory('offplan-pdfs') // مجلد الحفظ داخل storage
                        ->preserveFilenames()
                        ->downloadable(), // يسمح بتحميل الملف من Filament
                ])
                ->columns(1),

            Section::make('Amenities')
                ->description('Property amenities and facilities (primarily for off-plan properties)')
                ->schema([
                    Forms\Components\Repeater::make('amenities')
                        ->relationship('amenities')
                        ->schema([
                            Forms\Components\TextInput::make('name')
                                ->label('Amenity Name')
                                ->placeholder('e.g., Swimming Pool, Gym'),
                            Forms\Components\TextInput::make('icon_url')
                                ->label('Icon URL')
                                ->placeholder('https://example.com/icon.png'),
                            Forms\Components\Textarea::make('description')
                                ->label('Description')
                                ->placeholder('Brief description of the amenity'),
                            Forms\Components\TextInput::make('sort_order')
                                ->label('Sort Order')
                                ->numeric()
                                ->default(0),
                        ])
                        ->columns(2)
                        ->collapsible()
                        ->addActionLabel('Add Amenity')
                        ->deleteAction(fn ($action) => $action->requiresConfirmation()),
                ])
                ->collapsible(),

            Section::make('Floor Plans')
                ->description('Property floor plans and unit types (primarily for off-plan properties)')
                ->schema([
                    Forms\Components\Repeater::make('floorplans')
                        ->relationship('floorplans')
                        ->schema([
                            Forms\Components\TextInput::make('type')
                                ->label('Unit Type')
                                ->placeholder('e.g., 1 BR, 2 BR, Office'),
                            Forms\Components\FileUpload::make('plan_image_url')
                                ->label('Floor Plan Image')
                                ->acceptedFileTypes(['image/jpeg', 'image/jpg', 'image/png', 'image/webp'])
                                ->directory('floorplans'),
                            Forms\Components\FileUpload::make('pdf_url')
                                ->label('PDF Download')
                                ->acceptedFileTypes(['application/pdf'])
                                ->directory('floorplans-pdf'),
                            Forms\Components\TextInput::make('area')
                                ->label('Area (sq ft)')
                                ->numeric()
                                ->suffix('sq ft'),
                            Forms\Components\TextInput::make('price')
                                ->label('Price')
                                ->numeric()
                                ->prefix('د.إ')
                                ->maxValue(9999999999.99)
                                ->rule('max:9999999999.99')
                                ->reactive()
                                ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                    if ($state !== null && $state !== '') {
                                        $valueStr = number_format((float)$state, 2, '.', '');
                                        $integerPart = explode('.', $valueStr)[0];
                                        if (strlen($integerPart) > 10) {
                                            $set('price', null);
                                            \Filament\Notifications\Notification::make()
                                                ->title('Invalid Price')
                                                ->body('The price must not exceed 10 digits before the decimal point (maximum: 9999999999.99).')
                                                ->danger()
                                                ->send();
                                        }
                                    }
                                })
                                ->helperText('Maximum 10 digits before decimal point'),
                            Forms\Components\Textarea::make('description')
                                ->label('Description')
                                ->columnSpanFull(),
                            Forms\Components\TextInput::make('sort_order')
                                ->label('Sort Order')
                                ->numeric()
                                ->default(0),
                        ])
                        ->columns(3)
                        ->collapsible()
                        ->addActionLabel('Add Floor Plan')
                        ->deleteAction(fn ($action) => $action->requiresConfirmation()),
                ])
                ->collapsible(),

            Section::make('Nearby Places')
                ->description('Connectivity and nearby locations')
                ->schema([
                    Forms\Components\Repeater::make('nearbyPlaces')
                        ->relationship('nearbyPlaces')
                        ->schema([
                            Forms\Components\TextInput::make('place_name')
                                ->label('Place Name')
                                ->placeholder('e.g., Dubai Mall, Metro Station'),
                            Forms\Components\TextInput::make('time_minutes')
                                ->label('Time (minutes)')
                                ->numeric()
                                ->suffix('min'),
                            Forms\Components\TextInput::make('distance')
                                ->label('Distance')
                                ->placeholder('e.g., 5 km, 2.5 miles'),
                            Forms\Components\Select::make('transport_type')
                                ->label('Transport Type')
                                ->options([
                                    'By Car' => 'By Car',
                                    'Walking' => 'Walking',
                                    'Public Transport' => 'Public Transport',
                                    'Metro' => 'Metro',
                                ])
                                ->default('By Car'),
                            Forms\Components\TextInput::make('sort_order')
                                ->label('Sort Order')
                                ->numeric()
                                ->default(0),
                        ])
                        ->columns(2)
                        ->collapsible()
                        ->addActionLabel('Add Nearby Place')
                        ->deleteAction(fn ($action) => $action->requiresConfirmation()),
                ])
                ->collapsible(),

            Section::make('Unique Selling Points')
                ->description('Key features and selling points')
                ->schema([
                    Forms\Components\Repeater::make('uniquePoints')
                        ->relationship('uniquePoints')
                        ->schema([
                            Forms\Components\Textarea::make('point_text')
                                ->label('Unique Point')
                                ->placeholder('e.g., Prime location with sea view'),
                            Forms\Components\TextInput::make('icon_url')
                                ->label('Icon URL')
                                ->placeholder('https://example.com/icon.png'),
                            Forms\Components\TextInput::make('sort_order')
                                ->label('Sort Order')
                                ->numeric()
                                ->default(0),
                        ])
                        ->columns(1)
                        ->collapsible()
                        ->addActionLabel('Add Unique Point')
                        ->deleteAction(fn ($action) => $action->requiresConfirmation()),
                ])
                ->collapsible(),

            Section::make('Payment Schedule')
                ->description('Detailed payment plan breakdown (primarily for off-plan properties)')
                ->schema([
                    Forms\Components\Repeater::make('paymentSchedules')
                        ->relationship('paymentSchedules')
                        ->schema([
                            Forms\Components\TextInput::make('phase_name')
                                ->label('Phase Name')
                                ->placeholder('e.g., During Construction, On Handover'),
                            Forms\Components\TextInput::make('percentage')
                                ->label('Percentage')
                                ->numeric()
                                ->suffix('%')
                                ->minValue(0)
                                ->maxValue(100),
                            Forms\Components\DatePicker::make('due_date')
                                ->label('Due Date'),
                            Forms\Components\Textarea::make('description')
                                ->label('Description')
                                ->placeholder('Additional details about this payment phase'),
                            Forms\Components\TextInput::make('sort_order')
                                ->label('Sort Order')
                                ->numeric()
                                ->default(0),
                        ])
                        ->columns(2)
                        ->collapsible()
                        ->addActionLabel('Add Payment Phase')
                        ->deleteAction(fn ($action) => $action->requiresConfirmation()),
                ])
                ->collapsible(),

            Section::make('FAQs')
                ->description('Frequently asked questions about this property')
                ->schema([
                    Forms\Components\Repeater::make('faqs')
                        ->relationship('faqs')
                        ->schema([
                            Forms\Components\Textarea::make('question')
                                ->label('Question')
                                ->placeholder('e.g., What is the handover date?'),
                            Forms\Components\RichEditor::make('answer')
                                ->label('Answer')
                                ->placeholder('Detailed answer to the question')
                                ->columnSpanFull(),
                            Forms\Components\TextInput::make('sort_order')
                                ->label('Sort Order')
                                ->numeric()
                                ->default(0),
                        ])
                        ->columns(2)
                        ->collapsible()
                        ->addActionLabel('Add FAQ')
                        ->deleteAction(fn ($action) => $action->requiresConfirmation()),
                ])
                ->collapsible(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->label('Title')->searchable(),
                Tables\Columns\TextColumn::make('slug')->label('Slug')->searchable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('type.name')->label('Type')->searchable(),
                Tables\Columns\TextColumn::make('price')->label('Price')->money('AED', true),
                Tables\Columns\TextColumn::make('starting_price')->label('Starting Price')->money('AED', true)->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('handover_year')->label('Handover')->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('payment_plan')->label('Payment Plan')->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('region.name')->label('Region')->searchable(),
                Tables\Columns\TextColumn::make('developer.name')->label('Developer')->searchable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('has_location')
                    ->label('Map')
                    ->boolean()
                    ->getStateUsing(fn ($record) => $record->hasLocation())
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('latitude')
                    ->label('Latitude')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->formatStateUsing(fn ($state) => $state ? number_format($state, 6) : '-'),
                Tables\Columns\TextColumn::make('longitude')
                    ->label('Longitude')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->formatStateUsing(fn ($state) => $state ? number_format($state, 6) : '-'),
                Tables\Columns\IconColumn::make('is_sale')->label('Sale')->boolean(),
                Tables\Columns\IconColumn::make('is_home')->label('Home')->boolean(),
                Tables\Columns\TextColumn::make('reference_id')->label('Reference ID')->searchable(),
                Tables\Columns\TextColumn::make('created_at')->label('Created At')->dateTime(),
                Tables\Columns\TextColumn::make('updated_at')->label('Updated At')->dateTime(),
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
            'index' => Pages\ListProperties::route('/'),
            'create' => Pages\CreateProperty::route('/create'),
            'view' => Pages\ViewProperty::route('/{record}'),
            'edit' => Pages\EditProperty::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes([
            SoftDeletingScope::class,
        ]);
    }
}
