<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FamilyPearlCategoryResource\Pages;
use App\Models\FamilyPearlCategory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class FamilyPearlCategoryResource extends Resource
{
    protected static ?string $model = FamilyPearlCategory::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    protected static ?string $navigationLabel = 'Pērļu kategorijas';

    protected static ?string $navigationGroup = 'Ģimenes saturs';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')->required()->maxLength(100),
            Forms\Components\TextInput::make('slug')->required()->maxLength(120),
            Forms\Components\Toggle::make('is_active')->default(true),
            Forms\Components\TextInput::make('sort_order')->numeric()->default(0),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('slug'),
                Tables\Columns\ToggleColumn::make('is_active'),
                Tables\Columns\TextColumn::make('sort_order')->sortable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFamilyPearlCategories::route('/'),
            'create' => Pages\CreateFamilyPearlCategory::route('/create'),
            'edit' => Pages\EditFamilyPearlCategory::route('/{record}/edit'),
        ];
    }
}
