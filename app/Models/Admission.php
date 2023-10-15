<?php

namespace App\Models;

use App\Models\User;
use App\Models\Patient;
use App\Models\TreatmentPlan;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Admission extends Model
{
    use HasFactory;
    public function patient(){
        return $this->belongsTo(Patient::class);
    }
    public function treatmentplans(){
        return $this->hasMany(TreatmentPlan::class);
    }

    public function veterenarian(){
        return $this->belongsTo(User::class,'veterinarian_id');
    }

}
