<?php

namespace App\Filament\Client\Resources\AnimalResource\Pages;

use Filament\Actions;
use Illuminate\Database\Eloquent\Model;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Client\Resources\AnimalResource;

class CreateAnimal extends CreateRecord
{
    protected static string $resource = AnimalResource::class;

  

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    
}
