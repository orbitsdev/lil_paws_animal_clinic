<?php

namespace App\Filament\Resources\ClinicServicesResource\Pages;

use App\Filament\Resources\ClinicServicesResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateClinicServices extends CreateRecord
{
    protected static string $resource = ClinicServicesResource::class;
    protected static bool $canCreateAnother = false;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
