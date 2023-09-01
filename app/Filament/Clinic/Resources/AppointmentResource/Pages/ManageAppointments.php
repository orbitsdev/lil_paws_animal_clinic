<?php

namespace App\Filament\Clinic\Resources\AppointmentResource\Pages;

use App\Filament\Clinic\Resources\AppointmentResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageAppointments extends ManageRecords
{
    protected static string $resource = AppointmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
