<?php

namespace App\Filament\Resources\HeritageItemResource\Pages;

use App\Filament\Resources\HeritageItemResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateHeritageItem extends CreateRecord
{
    protected static string $resource = HeritageItemResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();

        return $data;
    }
}
