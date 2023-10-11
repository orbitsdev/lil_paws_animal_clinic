<?php

namespace App\Filament\Clinic\Resources\AnimalResource\Pages;

use App\Filament\Clinic\Resources\AnimalResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateAnimal extends CreateRecord
{
    protected static string $resource = AnimalResource::class;
}
