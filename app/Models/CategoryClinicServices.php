<?php

namespace App\Models;

use App\Models\Category;
use App\Models\ClinicServices;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CategoryClinicServices extends Model
{
    use HasFactory;

    public function clinicService(){
        return $this->belongsTo(ClinicServices::class, 'clinic_services_id');
    }
    public function category(){
        return $this->belongsTo(Category::class);
    }
}
