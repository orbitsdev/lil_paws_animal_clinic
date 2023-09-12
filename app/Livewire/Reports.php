<?php

namespace App\Livewire;

use App\Models\Patient;
use App\Models\Payment;
use Livewire\Component;
use App\Models\Category;
use App\Models\Appointment;

class Reports extends Component
{

    public $total_patients=0;
    public $total_appointments=0;
    public $total_revenue=0;
    public $upcoming_schedule;
    public $average_animal_category;
    public $categoriesWithAnimalsCount =[];
    public $topVeterenarian =[];

    public function render()
    {
        return view('livewire.reports');
    }

    public function mount(){
        $clinic_id = auth()->user()->clinic?->id;
        $now = now();
        $this->upcoming_schedule = Appointment::where('clinic_id', $clinic_id)
            ->whereYear('date', '=', $now->year)
            ->whereMonth('date', '=', $now->month)
            ->whereDate('date', '>=', $now)
            ->where('status', 'Accepted')
            ->get();
        
        $this->total_patients = Patient::whereMonth('created_at', $now->month)->where('clinic_id', auth()->user()->clinic?->id)->count();
        $this->total_appointments = Appointment::where('clinic_id', auth()->user()->clinic?->id)
            ->whereYear('date', '=', $now->year)
            ->whereMonth('date', '=', $now->month)
            ->where('status', 'Accepted')
            ->count();
        
        $this->total_revenue = Payment::where(function ($query) use ($now) {
            $query->whereHas('patient.appointment', function ($subQuery) use ($now) {
                $subQuery->whereIn('status', ['Accepted', 'Completed'])
                    ->where('clinic_id', auth()->user()->clinic?->id)
                    ->whereYear('date', '=', $now->year)
                    ->whereMonth('date', '=', $now->month);
            })
                ->orWhereHas('patient', function ($subQuery) use ($now) {
                    $subQuery->where('clinic_id', auth()->user()->clinic?->id)
                        ->whereDoesntHave('appointment');
                });
        })->orWhereHas('patient', function ($query) use ($now) {
            $query->where('clinic_id', auth()->user()->clinic?->id);
        })->sum('amount');
        
        $this->categoriesWithAnimalsCount = Category::whereHas('animals.patients', function ($query) use ($now) {
            $query->where('clinic_id', auth()->user()->clinic?->id);
        })->select('name')
            ->selectSub(function ($query) {
                $query->selectRaw('COUNT(*)')
                    ->from('animals')
                    ->whereColumn('category_id', 'categories.id');
            }, 'animal_count')
            ->get();
        

    }

    public function export(){
        dd('test');
    }
}
