<?php

namespace App\Models;

use App\Models\TreatmentPlan;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Monitor extends Model
{
    use HasFactory;


    public function treatmentPlan(){
        return $this->belongsTo(TreatmentPlan::class);
    }
}
