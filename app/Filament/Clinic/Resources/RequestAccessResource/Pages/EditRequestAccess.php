<?php

namespace App\Filament\Clinic\Resources\RequestAccessResource\Pages;

use App\Filament\Clinic\Resources\RequestAccessResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRequestAccess extends EditRecord
{
    protected static string $resource = RequestAccessResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
