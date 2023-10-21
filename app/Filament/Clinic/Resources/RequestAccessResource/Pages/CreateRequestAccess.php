<?php

namespace App\Filament\Clinic\Resources\RequestAccessResource\Pages;

use App\Filament\Clinic\Resources\RequestAccessResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateRequestAccess extends CreateRecord
{
    protected static string $resource = RequestAccessResource::class;
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
