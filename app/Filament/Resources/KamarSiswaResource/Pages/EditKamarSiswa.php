<?php

namespace App\Filament\Resources\KamarSiswaResource\Pages;

use App\Filament\Resources\KamarSiswaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditKamarSiswa extends EditRecord
{
    protected static string $resource = KamarSiswaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    
}
