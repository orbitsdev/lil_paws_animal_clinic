<?php

namespace App\Filament\Clinic\Resources\ExaminationResource\Pages;

use App\Filament\Clinic\Resources\ExaminationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListExaminations extends ListRecords
{
    protected static string $resource = ExaminationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
