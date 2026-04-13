<?php

namespace App\Filament\Resources\HeritageItemResource\Pages;

use App\Filament\Resources\HeritageItemResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListHeritageItems extends ListRecords
{
    protected static string $resource = HeritageItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
