<?php

namespace App\Models;

use App\Models\ClinicServices;
use Illuminate\Database\Eloquent\Model;
use App\Models\AllowedCategoryClinicServices;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AllowedCategory extends Model
{
    use HasFactory;


    public function clinic(){
        return $this->belongsTo(Clinic::class);
    }

    public function category(){
        return $this->belongsTo(category::class);
    }


    // allowed categories
    public function clinicServices(){
        return $this->belongsToMany(ClinicServices::class, 'allowed_category_clinic_services', 'allowed_category_id','clinic_services_id');
    }

    public function allowedCategoryClinicServices(){
        return $this->hasMany(AllowedCategoryClinicServices::class);
    }
    public function allowedCategoryClinicService(){
        return $this->hasOne(AllowedCategoryClinicServices::class);
    }
}
