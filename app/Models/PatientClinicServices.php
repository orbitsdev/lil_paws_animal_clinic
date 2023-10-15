<?php

namespace App\Models;

use App\Models\Patient;
use App\Models\ClinicServices;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PatientClinicServices extends Model
{
    use HasFactory;


    public function patient(){
        return $this->belongsTo(Patient::class);
    }
    
    public function clincService(){
        return $this->belongsTo(ClinicServices::class, 'clinic_services_id');
    }
}
