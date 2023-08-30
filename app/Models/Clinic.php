<?php

namespace App\Models;

use App\Models\User;
use App\Models\Veterinarian;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Clinic extends Model
{
    use HasFactory;

    public function veterinarians(){
        return $this->hasMany(Veterinarian::class);
    } 
}
