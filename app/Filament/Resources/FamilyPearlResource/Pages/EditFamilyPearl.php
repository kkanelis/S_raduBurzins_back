<?php

namespace App\Filament\Resources\FamilyPearlResource\Pages;

use App\Filament\Resources\FamilyPearlResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFamilyPearl extends EditRecord
{
    protected static string $resource = FamilyPearlResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (($data['status'] ?? null) === 'published') {
            $data['published_at'] = $this->record->published_at ?? now();
        }

        return $data;
    }
}
