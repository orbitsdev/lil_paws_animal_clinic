<?php

namespace App\Filament\Clinic\Resources\PaymentResource\Pages;

use App\Filament\Clinic\Resources\PaymentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPayments extends ListRecords
{
    protected static string $resource = PaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('New Record'),
        ];
    }
}
