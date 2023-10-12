<?php

namespace App\Models;

use App\Models\Clinic;
use App\Models\Patient;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ClinicServices extends Model
{
    use HasFactory;

    public function clinic(){

        return $this->belongsTo(Clinic::class);
        
    }

    public function patients(){
        return $this->belongsToMany(Patient::class,'patient_services','service_id','patient_id');
    }

}
