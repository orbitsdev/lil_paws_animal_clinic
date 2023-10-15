<?php

namespace App\Filament\Client\Resources\AppointmentResource\Pages;

use App\Filament\Client\Resources\AppointmentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAppointment extends EditRecord
{
    protected static string $resource = AppointmentResource::class;

    protected function getRedirectUrl(): string
{
    return $this->getResource()::getUrl('index');
}
    protected function getHeaderActions(): array
    {


        return [
            // Actions\DeleteAction::make(),
        ];
    }
}
