<?php

namespace App\Filament\Clinic\Resources\AppointmentResource\Pages;

use Filament\Resources\Pages\Page;
use Illuminate\Contracts\View\View;
use Illuminate\Contracts\Support\Htmlable;
use App\Filament\Clinic\Resources\AppointmentResource;

class AppointmentDetails extends Page
{
    protected static string $resource = AppointmentResource::class;

    protected static string $view = 'filament.clinic.resources.appointment-resource.pages.appointment-details';

   
    public function getHeader(): ?View
    {
        return view('filament.custom.cutom-header');
    }
   
    // public $record;
    // public function mount($record){
    //     dd($record);
    // }

}
