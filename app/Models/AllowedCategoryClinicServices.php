<?php

namespace App\Models;

use App\Models\ClinicServices;
use App\Models\AllowedCategory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AllowedCategoryClinicServices extends Model
{
    use HasFactory;

    public function allowed_category(){
        return $this->belongsTo(AllowedCategory::class, 'allowed_category_id');
    }
    public function clinic_services(){
        return $this->belongsTo(ClinicServices::class, 'clinic_services_id');
    }
}
