<?php

namespace App\Filament\Clinic\Resources\AllowedCategoryResource\Pages;

use App\Filament\Clinic\Resources\AllowedCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateAllowedCategory extends CreateRecord
{
    protected static string $resource = AllowedCategoryResource::class;
    protected static bool $canCreateAnother = false;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
