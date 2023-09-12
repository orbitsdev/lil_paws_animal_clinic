<?php

namespace App\Filament\Clinic\Resources\NoResource\Widgets;

use App\Models\Patient;
use App\Models\Payment;
use App\Models\Category;
use App\Models\Appointment;
use function Filament\Support\format_number;

use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class StatsOverview extends BaseWidget
{

    protected static bool $isLazy = true;
    // public $category;
    // public function mount(){
    //     $this->category = Category::withCount('animals')
    //     ->orderByDesc('animals_count')
    //     ->first();
        
    // }
    protected function getStats(): array
    {
        return [
            Stat::make(
                'Patients Record',  
                Patient::where(function ($query) {
                    $query->whereHas('appointment', function ($subQuery) {
                        $subQuery->whereIn('status', ['Accepted', 'Completed'])
                            ->where('clinic_id', auth()->user()->clinic?->id);
                    });
                })
                ->orWhereDoesntHave('appointment', function ($subQuery) {
                    $subQuery->where('clinic_id', auth()->user()->clinic?->id);
                })
                ->orWhere('clinic_id', auth()->user()->clinic?->id)
                ->count()
            )
            ->color('success'),
           
            Stat::make(
                'Total Appointments Accepted ',  
                Appointment::whereHas('clinic', function($query) {
                    $query->where('id', auth()->user()->clinic?->id)->where('status', 'Accepted');
                })->count()

            )
            ->color('success'),
            // Stat::make(
            //     'Total Appointments Completed',  
            //     Appointment::whereHas('clinic', function($query) {
            //         $query->where('id', auth()->user()->clinic?->id)->where('status', 'Completed');
            //     })->count()

            // )
            // ->color('success'),
            Stat::make(
                'Total Appointments Rejected',  
                Appointment::whereHas('clinic', function($query) {
                    $query->where('id', auth()->user()->clinic?->id)->where('status', 'Rejected');
                })->count()

            )
            ->color('success'),
            Stat::make(
                'Total Revenue ',  
               format_number( Payment::where(function($query) {
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
            })->sum('amount'))
            )
            ->color('success'),



       
        ];
    }
}
