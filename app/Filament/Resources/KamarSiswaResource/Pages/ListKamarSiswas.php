<?php

namespace App\Filament\Resources\KamarSiswaResource\Pages;

use App\Filament\Resources\KamarSiswaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListKamarSiswas extends ListRecords
{
    protected static string $resource = KamarSiswaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            
        ];
    }
}
