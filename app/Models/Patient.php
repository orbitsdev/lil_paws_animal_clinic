<?php

namespace App\Models;

use App\Models\User;
use App\Models\Animal;
use App\Models\Clinic;
use App\Models\Payment;
use App\Models\Service;
use App\Models\Admission;
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

    public function examination(){
        return $this->hasOne(Examination::class);
    }

    public function clinic(){
        return $this->belongsTo(Clinic::class);
    }
    

    public function payments(){
        return $this->hasMany(Payment::class);
    }

    // public function veterenarian(){
    //     return $this->hasOne(User::class);
    // }

    public function veterinarian(){
        return $this->belongsTo(User::class, 'veterinarian_id');
    }

    public function admissions(){
        return $this->hasMany(Admission::class);
    }
    public function admission(){
        return $this->hasOne(Admission::class);
    }


}
