<?php

namespace App\Filament\Resources\Biens\Pages;

use App\Filament\Resources\Biens\BienResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBiens extends ListRecords
{
    protected static string $resource = BienResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
