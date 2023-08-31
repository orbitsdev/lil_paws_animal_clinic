<?php

namespace App\Models;

use App\Models\Patient;
use App\Models\Appointment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Service extends Model
{
    use HasFactory;

    public function appointments(){
        return $this->belongsToMany(Appointment::class,'appointment_services','service_id','appointment_id');
    }

    public function patients(){
        return $this->belongsToMany(Patient::class,'patient_services','service_id','patient_id');
    }
}
