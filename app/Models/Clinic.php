<?php

namespace App\Models;

use App\Models\User;
use App\Models\Patient;
use App\Models\Payment;
use App\Models\Category;
use App\Models\Appointment;
use App\Models\Veterinarian;
use App\Models\ClinicServices;
use App\Models\AllowedCategory;
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


    // public function services(){
    //     return $this->hasMany(ClinicServices::class);
    // }

    public function categories(){
        return $this->hasMany(Category::class);
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function clinicServices(){
        return $this->hasMany(ClinicServices::class);
    }



    public function allowedCategory(){
        return $this->hasMany(AllowedCategory::class);
    }
}
