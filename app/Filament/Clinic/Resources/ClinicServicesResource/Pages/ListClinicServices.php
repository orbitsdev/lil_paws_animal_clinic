<?php

namespace App\Filament\Clinic\Resources\ClinicServicesResource\Pages;

use App\Filament\Clinic\Resources\ClinicServicesResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListClinicServices extends ListRecords
{
    protected static string $resource = ClinicServicesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
