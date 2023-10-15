<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestAccess extends Model
{
    use HasFactory;

    public function fromClinic(){
        return $this->belongsTo(Clinic::class, 'from_clinic_id');
    }
    public function toClinic(){
        return $this->belongsTo(Clinic::class, 'to_clinic_id');
    }
    public function patient(){
        return $this->belongsTo(Patient::class);
    }
}
