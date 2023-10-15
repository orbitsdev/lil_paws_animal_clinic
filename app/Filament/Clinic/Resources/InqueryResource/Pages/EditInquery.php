<?php

namespace App\Filament\Clinic\Resources\InqueryResource\Pages;

use App\Filament\Clinic\Resources\InqueryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditInquery extends EditRecord
{
    protected static string $resource = InqueryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\DeleteAction::make(),
        ];
    }
}
