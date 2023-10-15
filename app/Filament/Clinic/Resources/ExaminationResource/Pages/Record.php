<?php

namespace App\Filament\Clinic\Resources\ExaminationResource\Pages;

use App\Models\Animal;
use Filament\Resources\Pages\Page;
use App\Filament\Clinic\Resources\ExaminationResource;

class Record extends Page
{
    protected static string $resource = ExaminationResource::class;

    protected static string $view = 'filament.clinic.resources.examination-resource.pages.record';

    public $record;

    // public function mount($record){

    //     $this->record = Animal::where('id', $record)->first();
    // }
}
