<?php

namespace App\Filament\Clinic\Resources\AllowedCategoryResource\Pages;

use App\Filament\Clinic\Resources\AllowedCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAllowedCategory extends EditRecord
{
    protected static string $resource = AllowedCategoryResource::class;
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    protected function getHeaderActions(): array
    {
        return [
        ];
    }
}
