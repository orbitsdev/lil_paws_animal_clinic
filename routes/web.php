<?php

use App\Models\User;
use App\Models\Clinic;
use App\Models\Patient;
use App\Models\Payment;
use App\Models\Appointment;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\AppointmentExport;
use App\Exports\ClinicPatientExport;
use App\Exports\ClinicPaymentExport;
use App\Http\Controllers\LogoutController;
use App\Http\Middleware\ClinicMiddleWare;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/upcoming-appointment', function () {
   
    return Excel::download(new AppointmentExport,  now()->format('F-Y').'-upcoming-appointment.xlsx');
});
Route::get('/total-patient', function () {
    return Excel::download(new ClinicPatientExport,  now()->format('F-Y').'-total-patients.xlsx');
});

Route::get('/total-revenue', function () {
 
    return Excel::download(new ClinicPaymentExport,  now()->format('F-Y').'-total-revenue.xlsx');
});
Route::get('/medical-record', function () {
        $patient = Patient::first();
        return view('layout.medical-record', [
            'patient' => $patient
        ]);
   
});





Route::get('/test-pdf/{patient}', function ($patient) {
      
    $patient = Patient::find($patient);
    $data = [
        'patient' => $patient
    ];
    
    $pdf = Pdf::loadView('layout.medical-record', $data);
    $filename = $patient->animal?->name.'-'.now()->format('m-y');
    return $pdf->download($filename.'.pdf');
})->middleware(['auth'])->name('download-medical-record');


Route::get('/clinic-request', function(){
    return view('clinic-request');   
})->middleware('auth')->name('clinic-request',['except' => ['login']]);

Route::post('/auth/logout',[LogoutController::class,'logout'])->name('logout.filament');