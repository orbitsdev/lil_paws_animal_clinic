<?php

namespace App\Models;

use App\Models\Animal;
use App\Models\Clinic;
use App\Models\Service;
use App\Models\ClinicServices;
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

    // public function clinicServices(){
    //     return $this->belongsToMany(ClinicServices::class, 'category_clinic_services', 'category_id' ,'clinic_services_id');
    // }

    // public function categoryClinicServices(){
    //     return $this->hasMany(CategoryClinicServices::class);
    // }
    // public function categoryClinicService(){
    //     return $this->hasOne(CategoryClinicServices::class);
    // }


}
