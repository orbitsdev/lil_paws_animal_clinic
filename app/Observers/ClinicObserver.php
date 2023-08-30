<?php

namespace App\Observers;

use App\Models\Clinic;
use Illuminate\Support\Facades\Storage;

class ClinicObserver
{
    /**
     * Handle the Clinic "created" event.
     */
    public function created(Clinic $clinic): void
    {
        //
    }

    /**
     * Handle the Clinic "updated" event.
     */
    public function updated(Clinic $clinic): void
    {
        //
    }

    /**
     * Handle the Clinic "deleted" event.
     */
    public function deleted(Clinic $clinic): void
    {
        if(!empty($clinic->image)){

            if(Storage::disk('public')->exists($clinic->image)){
                Storage::disk('public')->delete($clinic->image);
            }
        }
    }

    /**
     * Handle the Clinic "restored" event.
     */
    public function restored(Clinic $clinic): void
    {
        //
    }

    /**
     * Handle the Clinic "force deleted" event.
     */
    public function forceDeleted(Clinic $clinic): void
    {
        //
    }
}
