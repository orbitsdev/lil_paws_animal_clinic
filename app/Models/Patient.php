<?php

namespace App\Models;

use App\Models\Animal;
use App\Models\Service;
use App\Models\Appointment;
use App\Models\Examination;
use App\Models\PatientService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Patient extends Model
{
    use HasFactory;


    public function appointment(){
        return $this->belongsTo(Appointment::class);
    }


    public function animal(){
        return $this->belongsTo(Animal::class);
    }

    public function services(){
        return $this->belongsToMany(Service::class,'patient_services','patient_id','service_id');
    }

    public function patientServices(){
        return $this->hasMany(PatientService::class);
    }
    
    public function patientService(){
        return $this->hasOne(PatientService::class);
    }

    public function examinations(){
        return $this->hasMany(Examination::class);
    }
    
}
