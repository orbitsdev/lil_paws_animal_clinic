<?php

namespace App\Models;

use App\Models\Clinic;
use App\Models\Patient;
use App\Models\Category;
use App\Models\AllowedCategory;
use App\Models\PatientClinicServices;
use Illuminate\Database\Eloquent\Model;
use App\Models\AllowedCategoryClinicServices;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ClinicServices extends Model
{
    use HasFactory;

    public function clinic(){

        return $this->belongsTo(Clinic::class);
        
    }

    public function patients(){
        return $this->belongsTo(Patient::class, 'patient_clinic_services', 'clinic_services_id', 'patient_id');
    }

    
    public function patientClinicServices(){
        return $this->hasMany(PatientClinicServices::class);
    }
    public function patientClinicService(){
        return $this->hasOne(PatientClinicServices::class);
    }


    // public function categories(){
    //     return $this->belongsToMany(Category::class, 'category_clinic_services', 'clinic_services_id','category_id' );
    // }

    // public function categoryClinicServices(){
    //     return $this->hasMany(CategoryClinicServices::class ,'clinic_services_id');
    // }
    // public function categoryClinicService(){
    //     return $this->hasOne(CategoryClinicServices::class ,'clinic_services_id');
    // }

    public function allowedCategories(){
        return $this->belongsToMany(AllowedCategory::class, 'allowed_category_clinic_services', 'clinic_services_id', 'allowed_category_id');
    }

    public function allowedCategoryClinicServices(){
        return $this->hasMany(AllowedCategoryClinicServices::class);
    }
    public function allowedCategoryClinicService(){
        return $this->hasOne(AllowedCategoryClinicServices::class);
    }

    

}
