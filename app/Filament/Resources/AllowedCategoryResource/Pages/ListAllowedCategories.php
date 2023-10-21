<?php

namespace App\Filament\Resources\AllowedCategoryResource\Pages;

use App\Filament\Resources\AllowedCategoryResource;
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
