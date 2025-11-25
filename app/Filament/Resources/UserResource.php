<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'User Content';
    protected static ?string $navigationLabel = 'User';
    protected static ?string $pluralModelLabel = 'Users';
    protected static ?string $modelLabel = 'User';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')->label('Name')->required()->maxLength(255),
            Forms\Components\TextInput::make('email')->label('Email')->email()->maxLength(255),
            Forms\Components\TextInput::make('password')->label('Password')->password()->maxLength(255),
            Forms\Components\TextInput::make('register_id')->label('Register ID')->numeric(),
            Forms\Components\TextInput::make('address')->label('Address')->maxLength(255),
            Forms\Components\DatePicker::make('birthday')->label('Birthday'),
            Forms\Components\TextInput::make('phone')->label('Phone')->tel()->maxLength(255),
            Forms\Components\TextInput::make('gender')->label('Gender')->maxLength(255),
            Forms\Components\FileUpload::make('image_profile')->label('Profile Image')->image(),
            Forms\Components\Toggle::make('status')->label('Status')->required(),
            Forms\Components\DateTimePicker::make('email_verified_at')->label('Verified At'),
            Forms\Components\TextInput::make('custom_fields')->label('Custom Fields'),
            Forms\Components\TextInput::make('avatar_url')->label('Avatar URL')->maxLength(255),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('name')->label('Name')->searchable(),
            Tables\Columns\TextColumn::make('email')->label('Email')->searchable(),
            Tables\Columns\TextColumn::make('register_id')->label('Register ID')->sortable(),
            Tables\Columns\TextColumn::make('phone')->label('Phone')->searchable(),
            Tables\Columns\TextColumn::make('gender')->label('Gender')->searchable(),
            Tables\Columns\IconColumn::make('status')->label('Status')->boolean(),
            Tables\Columns\ImageColumn::make('image_profile')->label('Profile Image'),
            Tables\Columns\TextColumn::make('created_at')->label('Created At')->dateTime()->sortable(),
            Tables\Columns\TextColumn::make('updated_at')->label('Updated At')->dateTime()->sortable(),
        ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
//                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
//                    Tables\Actions\DeleteBulkAction::make(),
//                    Tables\Actions\ForceDeleteBulkAction::make(),
//                    Tables\Actions\RestoreBulkAction::make(),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'view' => Pages\ViewUser::route('/{record}'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes([
            SoftDeletingScope::class,
        ]);
    }
}
