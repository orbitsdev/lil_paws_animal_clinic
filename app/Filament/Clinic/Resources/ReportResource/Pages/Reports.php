<?php

namespace App\Filament\Clinic\Resources\ReportResource\Pages;

use App\Models\Animal;
use App\Models\Patient;
use App\Models\Payment;
use App\Models\Category;
use App\Models\Appointment;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;
use App\Filament\Clinic\Resources\ReportResource;
use Illuminate\Support\Facades\Auth;

class Reports extends Page
{
    protected static string $resource = ReportResource::class;

    protected static string $view = 'filament.clinic.resources.report-resource.pages.reports';

    public $total_patients=0;
    public $total_appointments=0;
    public $total_revenue=0;
    public $upcoming_schedule;
    public $average_animal_category;
    public $categoriesWithAnimalsCount =[];
    public $topVeterenarian =[];


    public function export(){
        dd('dasd');
    }

    public function mount(){
        $clinic_id = auth()->user()->clinic?->id;
        $this->upcoming_schedule = Appointment::where('clinic_id', $clinic_id)->whereDate('date', '> ', now())->where('status','Accepted')->get();
       
        $this->total_patients = Patient::where('clinic_id', auth()->user()->clinic?->id)->count();
        $this->total_appointments = Appointment::where('clinic_id', auth()->user()->clinic?->id)->where('status','Accepted')->count();
        $this->total_revenue = Payment::where(function($query) {
            $query->whereHas('patient.appointment', function($subQuery) {
                $subQuery->whereIn('status', ['Accepted', 'Completed'])
                    ->where('clinic_id', auth()->user()->clinic?->id);
            })
            ->orWhereHas('patient', function($subQuery) {
                $subQuery->where('clinic_id', auth()->user()->clinic?->id)
                    ->whereDoesntHave('appointment');
            });
        })->orWhereHas('patient', function($query) {
            $query->where('clinic_id', auth()->user()->clinic?->id);
        })->sum('amount');
                 
        $this->categoriesWithAnimalsCount = Category::whereHas('animals.patients', function($query){
            $query->where('clinic_id', auth()->user()->clinic?->id);
        })->select('name')
        ->selectSub(function ($query) {
            $query->selectRaw('COUNT(*)')
                ->from('animals')
                ->whereColumn('category_id', 'categories.id');
        }, 'animal_count')
        ->get();


    }


    // public function getHeader(): ?View
    // {
    //     return view('filament.custom.report');
    // }


    
}
