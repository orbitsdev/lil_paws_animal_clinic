<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AllowedCategory extends Model
{
    use HasFactory;


    public function clinic(){
        return $this->belongsTo(Clinic::class);
    }
    
    public function category(){
        return $this->belongsTo(category::class);
    }
}
