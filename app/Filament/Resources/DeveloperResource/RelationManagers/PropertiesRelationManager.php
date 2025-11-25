<?php

namespace App\Filament\Resources\DeveloperResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BooleanColumn;

class PropertiesRelationManager extends RelationManager
{
    protected static string $relationship = 'properties';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                TextColumn::make('title')->searchable(),
                TextColumn::make('type.name')->label('Type')->sortable(),
                TextColumn::make('region.name')->label('Region')->sortable(),
                TextColumn::make('agent.name')->label('Agent')->sortable(),
                TextColumn::make('price')
                    ->label('Price')
                    ->formatStateUsing(fn ($record) => $record->converted_price . ' ' . $record->currency_symbol)
                    ->sortable(),
                BooleanColumn::make('is_sale')->label('Is Sale'),
                TextColumn::make('created_at')->dateTime()->label('Created At')->sortable(),
            ])
            ->filters([
                Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('from')->label('From Date'),
                        Forms\Components\DatePicker::make('to')->label('To Date'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        return $query
                            ->when($data['from'] ?? null, fn ($q, $date) => $q->whereDate('created_at', '>=', $date))
                            ->when($data['to'] ?? null, fn ($q, $date) => $q->whereDate('created_at', '<=', $date));
                    }),

                Filter::make('is_sale')
                    ->label('For Sale')
                    ->query(fn (Builder $query) => $query->where('is_sale', true)),
            ])
            ->headerActions([
                // Tables\Actions\CreateAction::make(), // إذا حابب تفعّل الإنشاء
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                // Tables\Actions\EditAction::make(),
                // Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
