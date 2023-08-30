<?php

namespace App\Models;

use App\Models\User;
use App\Models\Clinic;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Veterinarian extends Model
{
    use HasFactory;

    
    public function user(){
        return $this->belongsTo(User::class);
    }
    
    public function clinic(){
        return $this->belongsTo(Clinic::class);
        
    }
}
