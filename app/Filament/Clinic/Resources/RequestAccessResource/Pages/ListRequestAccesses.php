<?php

namespace App\Filament\Clinic\Resources\RequestAccessResource\Pages;

use App\Filament\Clinic\Resources\RequestAccessResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRequestAccesses extends ListRecords
{
    protected static string $resource = RequestAccessResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Send New Request'),
        ];
    }
}
