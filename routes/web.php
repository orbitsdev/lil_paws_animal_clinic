<?php

use App\Models\Patient;
use App\Models\Payment;
use App\Models\Appointment;
use App\Exports\AppointmentExport;
use App\Exports\ClinicPatientExport;
use App\Exports\ClinicPaymentExport;
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
