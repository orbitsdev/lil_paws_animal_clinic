<?php

namespace App\Models;

use App\Models\Patient;
use App\Models\Prescription;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Examination extends Model
{
    use HasFactory;


    public function patient(){
        return $this->belongsTo(Patient::class);
    }
    public function prescriptions(){
        return $this->hasMany(Prescription::class);
    }
}
