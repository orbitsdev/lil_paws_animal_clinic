<?php

namespace App\Models;

use App\Models\Examination;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Prescription extends Model
{
    use HasFactory;

    public function examination(){
        return $this->belongsTo(Examination::class);
    }

    
}
