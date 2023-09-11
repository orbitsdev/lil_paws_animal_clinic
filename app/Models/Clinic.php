<?php

namespace App\Models;

use App\Models\User;
use App\Models\Patient;
use App\Models\Payment;
use App\Models\Appointment;
use App\Models\Veterinarian;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Clinic extends Model
{
    use HasFactory;

    public function veterinarians(){
        return $this->hasMany(Veterinarian::class);
    } 

    public function appointments(){
        return $this->hasMany(Appointment::class);
    }
    public function patients(){
        return $this->hasMany(Patient::class);
    }

    public function users(){
        return $this->hasMany(User::class);
    }

    public function payments(){
        return $this->hasMany(Payment::class);
    }



}
