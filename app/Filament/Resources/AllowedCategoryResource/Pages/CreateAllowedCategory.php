<?php

namespace App\Filament\Resources\AllowedCategoryResource\Pages;

use App\Filament\Resources\AllowedCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateAllowedCategory extends CreateRecord
{
    protected static string $resource = AllowedCategoryResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
