<?php

namespace App\Filament\Resources\Biens\Pages;

use App\Filament\Resources\Biens\BienResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditBien extends EditRecord
{
    protected static string $resource = BienResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
