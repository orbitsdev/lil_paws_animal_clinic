<?php

namespace App\Filament\Resources\AnimalResource\Pages;

use Filament\Actions;
use Illuminate\Contracts\View\View;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\AnimalResource;

class ListAnimals extends ListRecords
{
    protected static string $resource = AnimalResource::class;

  
   

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
    // public function getHeader(): ?View
    // {
    //     return view('filament.custom.cutom-header');
    // }
}
