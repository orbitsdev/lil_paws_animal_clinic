<?php

namespace App\Filament\Clinic\Resources\PaymentResource\Pages;

use Filament\Actions;
use Illuminate\Database\Eloquent\Model;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Clinic\Resources\PaymentResource;

class CreatePayment extends CreateRecord
{
    protected static string $resource = PaymentResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    protected function handleRecordCreation(array $data): Model
{   

    $data['clinic_id'] = auth()->user()->clinic?->id;
    return static::getModel()::create($data);
}
}
