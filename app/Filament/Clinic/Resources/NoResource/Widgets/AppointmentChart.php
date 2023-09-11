<?php

namespace App\Filament\Clinic\Resources\NoResource\Widgets;

use App\Models\Appointment;
use Illuminate\Support\Carbon;
use Filament\Widgets\ChartWidget;

class AppointmentChart extends ChartWidget
{
    protected static ?string $heading = 'Appointment Chart Per Month';

    protected static bool $isLazy = true;
    protected int | string | array $columnSpan = 'full';
    protected function getData(): array
    {

        $data = $this->fetchChartData();

        $labels = Appointment::whereYear('date', '=', now()->year)
            ->whereMonth('date', '=', now()->month)
            ->pluck('date')
            ->map(function ($date) {
                return Carbon::parse($date)->format('F j');
            })
            ->toArray();

        return [
            'datasets' => [
                [
                   
                    'label' => 'Appointments Per Day',
                    'data' => $data, // Use the data fetched from your model
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }


    protected function fetchChartData()
    {
        $currentMonth = now()->format('Y-m');

        // Query the Appointment model to get the count of appointments for each day of the current month
        $data = Appointment::whereHas('clinic', function ($query) {
            $query->where('id', auth()->user()->clinic->id);
        })
            ->whereYear('date', '=', now()->year)
            ->whereMonth('date', '=', now()->month)
            ->orderBy('date')
            ->get()
            ->groupBy(function ($date) {
                return Carbon::parse($date->date)->format('F j');
            })
            ->map(function ($item) {
                return $item->count(); // Count appointments for each day
            })
            ->values()
            ->toArray();

        return $data;
    }
}
