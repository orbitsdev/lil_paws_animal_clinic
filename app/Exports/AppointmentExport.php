<?php

namespace App\Exports;

use App\Models\Appointment;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use GrahamCampbell\Markdown\Facades\Markdown;
class AppointmentExport implements FromView
{
    public function view(): View
    {
        $collections =  Appointment::with(['patients','veterinarian','clinic'])->where('clinic_id', auth()->user()->clinic?->id)
        ->whereYear('date', '=', now()->year)
        ->whereMonth('date', '=', now()->month)
        ->whereDate('date', '>=', now())
        ->where('status', 'Accepted')
        ->get();
        // dd($collections->patients);
      
        return view('exports.clinic.appointment-upcoming-schedules', [
            'collections' => $collections
        ]);
    }
}
