<?php

namespace App\Filament\Resources\KamarResource\Pages;

use App\Filament\Resources\KamarResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateKamar extends CreateRecord
{
    protected static string $resource = KamarResource::class;

    protected function getCreateFormAction(): Actions\Action
    {
        return parent::getCreateFormAction()
            ->label('Simpan'); 
    }

    protected function getCreateAnotherFormAction(): Actions\Action
    {
        return parent::getCreateAnotherFormAction()
            ->label('Simpan & tambah lagi');
    }

    protected function getCancelFormAction(): Actions\Action
    {
        return parent::getCancelFormAction()
            ->label('Batal');
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
