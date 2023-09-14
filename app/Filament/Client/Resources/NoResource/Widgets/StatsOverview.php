<?php

namespace App\Filament\Client\Resources\NoResource\Widgets;

use App\Models\Animal;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class StatsOverview extends BaseWidget
{
    // protected function getHeaderWidgetsColumns(): int | array
    // {
    //     return 4;
    // }

    protected function getStats(): array
    {
        return [
            Stat::make(
                'Total Pets',  
                Animal::whereHas('user', function($query){
                    $query->where('id', auth()->user()->id);
                })->count()
            )
            ->color('success'),
        ];
    }
}
