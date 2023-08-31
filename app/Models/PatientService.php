<?php

namespace App\Models;

use App\Models\Patient;
use App\Models\Service;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PatientService extends Model
{
    use HasFactory;


    public function patient(){
        return $this->belongsTo(Patient::class);
    }
    public function service(){
        return $this->belongsTo(Service::class);
    }

}
