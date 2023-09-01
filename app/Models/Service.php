<?php

namespace App\Models;

use App\Models\Patient;
use App\Models\Appointment;
use App\Models\PatientService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Service extends Model
{
    use HasFactory;

   

    public function patients(){
        return $this->belongsToMany(Patient::class,'patient_services','service_id','patient_id');
    }

    public function patientServices(){
        return $this->hasMany(PatientService::class);
    }
}
