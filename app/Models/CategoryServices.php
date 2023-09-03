<?php

namespace App\Models;

use App\Models\Service;
use App\Models\Category;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CategoryServices extends Model
{
    use HasFactory;

    public function service(){
        return $this->belongsTo(Service::class);
    }
    public function category(){
        return $this->belongsTo(Category::class);
    }
}
