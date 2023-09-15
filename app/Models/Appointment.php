<?php

namespace App\Models;

use App\Models\User;
use App\Models\Animal;
use App\Models\Patient;
use App\Models\Service;
use App\Models\Veterinarian;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Appointment extends Model
{
    use HasFactory;



    public function clinic(){
        return $this->belongsTo(Clinic::class);
    }


    public function services(){
        return $this->belongsToMany(Service::class,'appointment_services','appointment_id','service_id')->withPivot(['description']);
    }

    

    public function patients(){
        return $this->hasMany(Patient::class);
    }

    public function patient(){
        return $this->hasOne(Patient::class);
    }

    public function hasStatus($allowedStatus)
    {
        return in_array($this->status, $allowedStatus);
    }
    

   

    public function clientAppointments()
    {
        return $this->hasMany(Appointment::class, 'client_id');
    }
    public function veterinarianAppointments()
    {
        return $this->hasMany(Appointment::class, 'veterinarian_id');
    }

    public function user(){
        return $this->belongsTo(User::class);
    }
    public function veterinarian(){
        return $this->belongsTo(User::class);
    }




    

}
