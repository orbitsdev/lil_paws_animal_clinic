<?php

namespace App\Filament\Resources\VeterinarianResource\Pages;

use App\Filament\Resources\VeterinarianResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;



class CreateVeterinarian extends CreateRecord
{
    protected static string $resource = VeterinarianResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

}
