<?php

namespace App\Filament\Clinic\Resources\ExaminationResource\Pages;

use Filament\Actions;
use Illuminate\Database\Eloquent\Model;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Clinic\Resources\ExaminationResource;

class CreateExamination extends CreateRecord
{
    protected static string $resource = ExaminationResource::class;
    protected static bool $canCreateAnother = false;
    protected function handleRecordCreation(array $data): Model
{

    $data['clinic_id'] = auth()->user()->clinic?->id;
    $patient = static::getModel()::create($data);
    return $patient;

}

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
