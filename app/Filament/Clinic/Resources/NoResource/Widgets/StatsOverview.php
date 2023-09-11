<?php

namespace App\Filament\Clinic\Resources\NoResource\Widgets;

use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Payment;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

use function Filament\Support\format_number;

class StatsOverview extends BaseWidget
{

    protected static bool $isLazy = true;
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
            Stat::make(
                'Total Appointments ',  
                Appointment::whereHas('clinic', function($query) {
                    $query->where('id', auth()->user()->clinic?->id)->whereIn('status', ['Accepted','Completed']);
                })->count()

            )
            ->color('success'),


       
        ];
    }
}
