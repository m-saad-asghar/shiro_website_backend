<?php

namespace App\Filament\Resources\SaleAgentResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class UserPaymentRelationManager extends RelationManager
{
    protected static string $relationship = 'userPayments';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('amount')
                ->required()
                ->numeric()
                ->label('Amount')
                ->afterStateUpdated(function ($state, callable $set, RelationManager $livewire) {
                    $record = $livewire->ownerRecord;
                    $price = $record->price;

                    $previous = $record->userPayments()
                        ->whereIn('status', ['pending', 'paid'])
                        ->sum('amount');

                    if ($state == 0) {
                        $set('amount', null); // لا تقبل الصفر
                        return;
                    }

                    $remaining = $price - $previous;

                    if ($state > $remaining) {
                        $set('amount', $remaining); // اضبط المبلغ على الحد الأقصى المتاح
                    }
                })
                ->disabled(function (RelationManager $livewire) {
                    $record = $livewire->ownerRecord;
                    $price = $record->price;

                    $totalPaid = $record->userPayments()
                        ->whereIn('status', ['pending', 'paid'])
                        ->sum('amount');

                    return $totalPaid >= $price;
                }),

            Forms\Components\Select::make('status')
                ->options([
                    'pending' => 'Pending',
                    'paid' => 'Paid',
                    'failed' => 'Failed',
                ])
                ->default('pending')
                ->disabled()
                ->label('Status'),

            Forms\Components\DatePicker::make('paid_at')
                ->label('Paid At')
                ->disabled()
                ->hidden(true),

            Forms\Components\Textarea::make('note')
                ->label('Note')
                ->maxLength(500),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('amount')
                    ->money()
                    ->label('Amount'),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->colors([
                        'primary' => 'pending',
                        'success' => 'paid',
                        'danger' => 'failed',
                    ]),

                Tables\Columns\TextColumn::make('paid_at')
                    ->date()
                    ->label('Paid At'),

                Tables\Columns\TextColumn::make('note')
                    ->limit(20)
                    ->tooltip(fn ($record) => $record->note),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Add Payment')
                    ->visible(function (RelationManager $livewire) {
                        $record = $livewire->ownerRecord;
                        $price = $record->price;

                        $totalPaid = $record->userPayments()
                            ->whereIn('status', ['pending', 'paid'])
                            ->sum('amount');

                        return $totalPaid < $price;
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn ($record) => $record->status === 'pending'),

                Tables\Actions\DeleteAction::make()
                    ->visible(fn ($record) => $record->status === 'pending'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn ($records) =>
                            $records && $records->every(fn ($record) => $record->status === 'pending')
                        ),
                ]),
            ]);
    }
}
