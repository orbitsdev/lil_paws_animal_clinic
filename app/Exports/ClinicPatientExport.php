<?php

namespace App\Exports;

use App\Models\Patient;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
class ClinicPatientExport  implements FromView
{
    public function view(): View
    {
        $collections =  Patient::whereMonth('created_at', now()->month)->where('clinic_id', auth()->user()->clinic?->id)->get();
    
        return view('exports.clinic.appointment-patient', [
            'collections' => $collections
        ]);
    }
}
