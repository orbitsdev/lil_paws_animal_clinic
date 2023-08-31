<?php

namespace App\Filament\Client\Resources\AppointmentResource\Pages;

use App\Filament\Client\Resources\AppointmentResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageAppointments extends ManageRecords
{
    protected static string $resource = AppointmentResource::class;

    

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // $data['user_id'] = auth()->id();
        dd($data);
     
        return $data;
    }
    
        protected function handleRecordCreation(array $data): Model
    {
        dd($data);
        return static::getModel()::create($data);
    }
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
