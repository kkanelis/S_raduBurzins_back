<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\TernaryFilter;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('first_name')
                    ->required()
                    ->label('First Name'),
                Forms\Components\TextInput::make('last_name')
                    ->required()
                    ->label('Last Name'),
                Forms\Components\TextInput::make('nickname')
                    ->label('Nickname'),
                Forms\Components\TextInput::make('phone')
                    ->tel()
                    ->label('Phone Number'),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required(),
                Forms\Components\Toggle::make('is_approved')
                    ->required(),
                Forms\Components\Toggle::make('is_admin')
                    ->required()
                    ->label('Can Access Admin Panel'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('first_name')
                    ->label('Vārds')
                    ->searchable(),
                TextColumn::make('last_name')
                    ->label('Uzvārds')
                    ->searchable(),
                TextColumn::make('nickname')
                    ->label('Lietotājvārds')
                    ->searchable(),
                TextColumn::make('phone')
                    ->label('Telefona numurs')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('E-pasts'),
                ToggleColumn::make('is_approved')
                    ->label('Apstiprināts'),
                ToggleColumn::make('is_admin')
                    ->label('Admin privilēģijas'),
                TextColumn::make('created_at')
                    ->dateTime(),
            ])
            ->filters([
                TernaryFilter::make('is_approved'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}