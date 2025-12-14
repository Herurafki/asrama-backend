<?php

namespace App\Filament\Resources\OrangTuaResource\Pages;

use App\Filament\Resources\OrangTuaResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateOrangTua extends CreateRecord
{
    protected static string $resource = OrangTuaResource::class;

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
