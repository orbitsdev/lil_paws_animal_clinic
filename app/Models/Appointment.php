<?php

namespace App\Models;

use App\Models\Veterinarian;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Appointment extends Model
{
    use HasFactory;

    public function client(){
        return $this->belongTo(User::class);
    }
    public function veterenarian(){
        return $this->belongTo(Veterinarian::class);
    }

}
