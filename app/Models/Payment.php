<?php

namespace App\Models;

use App\Models\Clinic;
use App\Models\Patient;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payment extends Model
{
    use HasFactory;

    public function patient(){
        return $this->belongsTo(Patient::class);
    }
    public function clinic(){
        return $this->belongsTo(Clinic::class);
    }
}
