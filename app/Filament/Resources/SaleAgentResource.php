<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SaleAgentResource\Pages;
use App\Filament\Resources\SaleAgentResource\RelationManagers\UserPaymentRelationManager;
use App\Filament\Resources\SaleAgentResource\Widgets\AgentsSalesOverview;
use App\Models\Agent;
use App\Models\Property;
use App\Models\SaleAgent;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SaleAgentResource extends Resource
{
    protected static ?string $model = SaleAgent::class;
    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationGroup = 'Sales';
    protected static ?string $navigationLabel = 'Agents Sales';
    protected static ?string $pluralModelLabel = 'Agents Sales';
    protected static ?string $modelLabel = 'Sale Agent';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('agent_id')
                ->label('Agent')
                ->options(Agent::all()->pluck('name', 'id'))
                ->searchable()
                ->required()
                ->reactive()
                ->disabled(fn ($record) => $record && $record->userPayments()->exists()),

            Forms\Components\Select::make('property_id')
                ->label('Property')
                ->options(function (callable $get) {
                    $agentId = $get('agent_id');
                    if (!$agentId) return [];
                    return Property::where('agent_id', $agentId)->pluck('title', 'id');
                })
                ->searchable()
                ->required()
                ->disabled(fn ($get, $record) => !$get('agent_id') || ($record && $record->userPayments()->exists()))
                ->reactive()
                ->afterStateUpdated(function ($state, callable $set) {
                    $property = Property::find($state);
                    if ($property) {
                        $set('price', $property->price);
                    }
                }),

            Forms\Components\TextInput::make('price')
                ->numeric()
                ->prefix('$')
                ->required()
                ->disabled(fn ($record) => $record && $record->userPayments()->exists()),

            Forms\Components\Select::make('user_id')
                ->label('User')
                ->options(User::all()->pluck('name', 'id'))
                ->searchable()
                ->required()
                ->reactive()
                ->disabled(fn ($record) => $record && $record->userPayments()->exists()),

            Forms\Components\DatePicker::make('date')
                ->disabled(fn ($record) => $record && $record->userPayments()->exists()),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('agent.name')->label('Agent')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('property.title')->label('Property')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('user.name')->label('User')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('price')->money()->sortable(),
                Tables\Columns\TextColumn::make('date')->date()->sortable(),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('agent_id')->label('Agent')->options(Agent::all()->pluck('name', 'id')),
                Tables\Filters\SelectFilter::make('property_id')->label('Property')->options(Property::all()->pluck('title', 'id')),
                Tables\Filters\TrashedFilter::make(),
                Tables\Filters\Filter::make('date')->form([
                    Forms\Components\DatePicker::make('from')->label('From'),
                    Forms\Components\DatePicker::make('to')->label('To'),
                ])->query(function (Builder $query, array $data) {
                    if (!empty($data['from'])) {
                        $query->where('date', '>=', $data['from']);
                    }
                    if (!empty($data['to'])) {
                        $query->where('date', '<=', $data['to']);
                    }
                    return $query;
                }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(), // دائمًا ظاهر
                Tables\Actions\DeleteAction::make()
                    ->visible(fn ($record) =>
                        $record->userPayments()->count() === 0
                    ),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn ($records) =>
                            $records && $records->every(fn ($record) =>
                                $record->userPayments()->count() === 0
                            )),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            UserPaymentRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListSaleAgents::route('/'),
            'create' => Pages\CreateSaleAgent::route('/create'),
            'view'   => Pages\ViewSaleAgent::route('/{record}'),
            'edit'   => Pages\EditSaleAgent::route('/{record}/edit'),
        ];
    }

    public static function getWidgets(): array
    {
        return [
            AgentsSalesOverview::class,
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes([SoftDeletingScope::class]);
    }
}
