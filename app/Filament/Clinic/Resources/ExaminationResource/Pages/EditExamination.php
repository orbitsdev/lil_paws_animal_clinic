<?php

namespace App\Filament\Clinic\Resources\ExaminationResource\Pages;

use App\Filament\Clinic\Resources\ExaminationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditExamination extends EditRecord
{
    protected static string $resource = ExaminationResource::class;
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
