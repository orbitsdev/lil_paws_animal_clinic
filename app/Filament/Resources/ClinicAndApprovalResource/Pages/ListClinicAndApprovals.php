<?php

namespace App\Filament\Resources\ClinicAndApprovalResource\Pages;

use App\Filament\Resources\ClinicAndApprovalResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListClinicAndApprovals extends ListRecords
{
    protected static string $resource = ClinicAndApprovalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
