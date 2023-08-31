<?php

namespace App\Models;

use App\Models\Service;
use App\Models\Appointment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Patient extends Model
{
    use HasFactory;


    public function appointment(){
        return $this->belongsTo(Appointment::class);
    }


    public function services(){
        return $this->belongsToMany(Service::class,'patient_services','patient_id','service_id');
    }
}
