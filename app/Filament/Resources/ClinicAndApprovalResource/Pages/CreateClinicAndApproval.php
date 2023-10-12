<?php

namespace App\Filament\Resources\ClinicAndApprovalResource\Pages;

use App\Filament\Resources\ClinicAndApprovalResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateClinicAndApproval extends CreateRecord
{
    protected static string $resource = ClinicAndApprovalResource::class;
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
