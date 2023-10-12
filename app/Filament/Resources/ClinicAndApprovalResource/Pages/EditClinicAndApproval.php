<?php

namespace App\Filament\Resources\ClinicAndApprovalResource\Pages;

use App\Filament\Resources\ClinicAndApprovalResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditClinicAndApproval extends EditRecord
{
    protected static string $resource = ClinicAndApprovalResource::class;
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
