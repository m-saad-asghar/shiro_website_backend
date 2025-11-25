<?php

namespace App\Filament\Resources\AgentResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SalesRelationManager extends RelationManager
{

    protected static string $relationship = 'sales';

    protected static ?string $recordTitleAttribute = 'id';

    public  function form(Forms\Form $form): Form
    {
        return $form->schema([
            // حقول المبيعات التي تريد عرضها وتحريرها
            Forms\Components\TextInput::make('property_id')->required(),
            Forms\Components\TextInput::make('price')->numeric()->required(),
            Forms\Components\DatePicker::make('date')->required(),
        ]);
    }

    public  function table(Tables\Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('property.title')->label('Property'),
                Tables\Columns\TextColumn::make('price')->label('Price')->money('usd', true),
                Tables\Columns\TextColumn::make('date')->dateTime()->label('Sale Date'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
}
