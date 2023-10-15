<?php

namespace App\Exports;

use App\Models\Payment;
use Maatwebsite\Excel\Concerns\FromCollection;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
class ClinicPaymentExport implements FromView
{
    public function view(): View
    {
        $collections = Payment::whereHas('patient.appointment', function($query){
            $query->whereIn('status',['Accepted', 'Completed'])->where('clinic_id',auth()->user()->clinic?->id);
        })
        ->orWhereDoesntHave('patient.appointment') 
        ->whereMonth('created_at', now()->month) 
        ->where('clinic_id', auth()->user()->clinic?->id) 
        ->get();
       
        
        
        return view('exports.clinic.clinic-revenue', [
            'collections' => $collections
        ]);
    }
}
