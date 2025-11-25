<?php

namespace App\Filament\Resources\AgentResource\RelationManagers;

use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ContactAgentFormsRelationManager extends RelationManager
{
    protected static string $relationship = 'contactAgentForms';

    protected static ?string $recordTitleAttribute = 'first_name';

    // ✅ بدون static
    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('first_name')->label('First Name')->searchable(),
                Tables\Columns\TextColumn::make('second_name')->label('Second Name')->searchable(),
                Tables\Columns\TextColumn::make('phone_one')->label('Phone One'),
                Tables\Columns\TextColumn::make('phone_two')->label('Phone Two'),
                Tables\Columns\TextColumn::make('message')->label('Message')->wrap(),
                Tables\Columns\TextColumn::make('property.title')->label('Property')->default('-'),
                Tables\Columns\TextColumn::make('created_at')->label('Sent At')->dateTime()->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                // لا حاجة لإنشاء رسالة من الـ Panel
                // Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
}
