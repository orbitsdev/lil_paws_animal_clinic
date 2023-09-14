<?php

namespace App\Filament\Resources\NoResource\Widgets;

use App\Models\Patient;
use App\Models\Payment;
use App\Models\Appointment;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make(
                'Patients Record',  
                Patient::count()
            )
            ->color('success'),
           
            Stat::make(
                'Total Appointments Accepted ',  
                Appointment::count()

            )
            ->color('success'),
          
            Stat::make(
                'Total Appointments Rejected',  
                Appointment::where('status', 'Rejected')->count()

            )
            ->color('success'),
            Stat::make(
                'Total Revenue ',  
               number_format(Payment::sum('amount'))
            )
            ->color('success'),
        ];
    }
}
