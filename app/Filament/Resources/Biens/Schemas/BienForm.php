<?php

declare(strict_types=1);

namespace App\Filament\Resources\Biens\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class BienForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('equipo')
                    ->required(),
                TextInput::make('marca')
                    ->default(null),
                TextInput::make('modelo')
                    ->default(null),
                TextInput::make('serial')
                    ->default(null),
                TextInput::make('color')
                    ->default(null),
                TextInput::make('numero_bien')
                    ->required(),
                TextInput::make('tipo_bien')
                    ->required(),
                TextInput::make('estado')
                    ->required(),
                Textarea::make('observaciones')
                    ->default(null)
                    ->columnSpanFull(),
                TextInput::make('ubicacion')
                    ->required(),
            ]);
    }
}
