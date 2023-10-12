<?php

namespace App\Models;

use App\Models\Animal;
use App\Models\Clinic;
use App\Models\Service;
use App\Models\AllowedCategory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory;

    public function services(){
        return $this->belongsToMany(Service::class, 'category_services', 'category_id' ,'service_id');
    }
    
    

    public function animals(){
        return $this->hasMany(Animal::class);
    }

    public function clinic(){
        return $this->belongsTo(Clinic::class);
    }

    public function allowed_categories(){
        return $this->hasMany(AllowedCategory::class);
    }
}
