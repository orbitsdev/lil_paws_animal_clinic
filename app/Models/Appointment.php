<?php

namespace App\Models;

use App\Models\Animal;
use App\Models\Patient;
use App\Models\Service;
use App\Models\Veterinarian;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Appointment extends Model
{
    use HasFactory;



    public function clinic(){
        return $this->belongsTo(Clinic::class);
    }


    public function services(){
        return $this->belongsToMany(Service::class,'appointment_services','appointment_id','service_id')->withPivot(['description']);
    }

    

    public function patients(){
        return $this->hasMany(Patient::class);
    }
    public function patient(){
        return $this->hasOne(Patient::class);
    }

    

}
