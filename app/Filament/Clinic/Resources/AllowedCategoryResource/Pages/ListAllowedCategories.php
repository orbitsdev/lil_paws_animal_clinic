<?php

namespace App\Filament\Clinic\Resources\AllowedCategoryResource\Pages;

use App\Filament\Clinic\Resources\AllowedCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAllowedCategories extends ListRecords
{
    protected static string $resource = AllowedCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
