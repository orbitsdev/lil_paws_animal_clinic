<?php

namespace App\Filament\Client\Resources\AnimalResource\Pages;

use App\Filament\Client\Resources\AnimalResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAnimal extends EditRecord
{
    protected static string $resource = AnimalResource::class;


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
