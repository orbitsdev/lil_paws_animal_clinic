<?php

namespace App\Filament\Resources\ClinicServicesResource\Pages;

use App\Filament\Resources\ClinicServicesResource;
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
