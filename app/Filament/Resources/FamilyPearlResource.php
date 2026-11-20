<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FamilyPearlResource\Pages;
use App\Models\FamilyPearl;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class FamilyPearlResource extends Resource
{
    protected static ?string $model = FamilyPearl::class;

    protected static ?string $navigationIcon = 'heroicon-o-sparkles';

    protected static ?string $navigationLabel = 'Radu pērles';

    protected static ?string $navigationGroup = 'Ģimenes saturs';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('user_id')
                ->relationship('user', 'first_name')
                ->getOptionLabelFromRecordUsing(fn ($record) => $record->full_name)
                ->required()
                ->disabled(),
            Forms\Components\Select::make('category_id')
                ->relationship('category', 'name')
                ->required(),
            Forms\Components\TextInput::make('title')
                ->required()
                ->maxLength(160),
            Forms\Components\Textarea::make('text')
                ->required()
                ->rows(5),
            Forms\Components\Textarea::make('context')
                ->rows(4),
            Forms\Components\Toggle::make('is_anonymous')
                ->disabled(),
            Forms\Components\Select::make('status')
                ->options([
                'published' => 'Publicēta',
                'rejected' => 'Noraidīta',
                ])
                ->required(),
            Forms\Components\Textarea::make('moderation_note')
                ->rows(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->limit(50),
                Tables\Columns\TextColumn::make('category.name')
                    ->badge(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'published' => 'success',
                        'rejected' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('is_anonymous')
                    ->label('Anonīma')
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Jā' : 'Nē'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'published' => 'Publicēta',
                        'rejected' => 'Noraidīta',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFamilyPearls::route('/'),
            'edit' => Pages\EditFamilyPearl::route('/{record}/edit'),
        ];
    }
}
