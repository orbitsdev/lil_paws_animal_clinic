<?php

namespace App\Filament\Resources\ReportResource\Pages;

use App\Models\Patient;
use App\Models\Payment;
use App\Models\Category;
use App\Models\Appointment;
use Filament\Resources\Pages\Page;
use App\Filament\Resources\ReportResource;

class Reports extends Page
{
    protected static string $resource = ReportResource::class;

    protected static string $view = 'filament.resources.report-resource.pages.reports';

    public $total_patients=0;
    public $total_appointments=0;
    public $total_revenue=0;
    public $upcoming_schedule;
    public $average_animal_category;
    public $categoriesWithAnimalsCount =[];
    public $topVeterenarian =[];

    public function mount(){
        $this->upcoming_schedule = Appointment::whereDate('date', '> ', now())->where('status','Accepted')->get();
        $this->total_patients = Patient::count();
        $this->total_appointments = Appointment::where('status','Accepted')->count();
        $this->total_revenue = Payment::whereHas('patient.appointment')->orWhereHas('patient')->sum('amount');
        $this->categoriesWithAnimalsCount = Category::whereHas('animals.patients')->select('name')
        ->selectSub(function ($query) {
            $query->selectRaw('COUNT(*)')
                ->from('animals')
                ->whereColumn('category_id', 'categories.id');
        }, 'animal_count')
        ->get();


    }
}
