<?php

namespace App\Filament\Client\Resources\AppointmentResource\Pages;

use Filament\Actions;
use App\Models\Patient;
use Illuminate\Database\Eloquent\Model;
use Filament\Resources\Pages\ManageRecords;
use App\Filament\Client\Resources\AppointmentResource;

class ManageAppointments extends ManageRecords
{
    protected static string $resource = AppointmentResource::class;



    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // $data['user_id'] = auth()->id();
      

        return $data;
    }

    protected function handleRecordCreation(array $data): Model
    {
        return static::getModel()::create($data);
    }
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->using(function (array $data, string $model): Model {



                $appointment = $model::create($data);

                return $appointment;
            })

                ->after(function ($record) {
                    $patients = Patient::where('appointment_id', $record->id)->get();

                    foreach ($patients as $patient) {
                        $patient->clinic_id = $record->clinic_id; // Set the clinic_id from the appointment
                        $patient->save();
                    }
                })
        ];
    }
}
