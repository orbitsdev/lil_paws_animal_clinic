<?php

namespace App\Filament\Clinic\Resources\ClinicServicesResource\Pages;

use App\Filament\Clinic\Resources\ClinicServicesResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditClinicServices extends EditRecord
{
    protected static string $resource = ClinicServicesResource::class;
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
