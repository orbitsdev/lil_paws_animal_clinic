<?php

namespace App\Models;

use App\Models\Service;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory;

    public function services(){
        return $this->belongsToMany(Service::class, 'category_services', 'category_id' ,'service_id');
    }
    
}
