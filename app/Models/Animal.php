<?php

namespace App\Models;

use App\Models\User;
use App\Models\Patient;
use App\Models\Category;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Animal extends Model
{
    use HasFactory;

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function patients(){
        return $this->hasMany(Patient::class);
     }

     public function category(){
        return $this->belongsTo(Category::class);
     }
}
