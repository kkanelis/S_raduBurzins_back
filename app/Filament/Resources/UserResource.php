<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use App\Models\SpecialDay;
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
                Forms\Components\DatePicker::make('date_of_birth')
                    ->required()
                    ->maxDate(now())
                    ->label('Date of Birth')
                    ->afterStateUpdated(function ($record, $state) {
                        if ($record && $state) {
                            // Find existing birthday special day
                            $birthday = SpecialDay::where('user_id', $record->id)
                                ->where('title', $record->name . ' svin dzimšanas dienu!')
                                ->first();

                            if ($birthday) {
                                // Update existing birthday
                                $birthday->update([
                                    'date' => $state,
                                    'description' => ''
                                ]);
                            } else {
                                // Create new birthday special day
                                SpecialDay::create([
                                    'user_id' => $record->id,
                                    'title' => $record->name . ' svin dzimšanas dienu!',
                                    'description' => '',
                                    'date' => $state,
                                    'repeats' => true,
                                    'is_approved' => true
                                ]);
                            }
                        }
                    }),
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
                TextColumn::make('date_of_birth')
                    ->label('Dzimšanas datums')
                    ->date(),
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