<?php

namespace App\Filament\Resources\PerizinanResource\Pages;

use App\Filament\Resources\PerizinanResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePerizinan extends CreateRecord
{
    protected static string $resource = PerizinanResource::class;

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
