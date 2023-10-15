<?php

namespace App\Models;

use App\Models\Monitor;
use App\Models\Admission;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TreatmentPlan extends Model
{
    use HasFactory;

    public function admission(){
        return $this->belongsTo(Admission::class);
    }

    public function monitors(){
        return $this->hasMany(Monitor::class);
    }
}
