<?php

namespace App\Observers;

use App\Models\Veterinarian;
use Illuminate\Support\Facades\Storage;

class VeterinarianObserver
{
    /**
     * Handle the Veterinarian "created" event.
     */
    public function created(Veterinarian $veterinarian): void
    {
        //
    }

    /**
     * Handle the Veterinarian "updated" event.
     */
    public function updated(Veterinarian $veterinarian): void
    {
        //
    }

    /**
     * Handle the Veterinarian "deleted" event.
     */
    public function deleted(Veterinarian $veterinarian): void
    {
        if(!empty($veterinarian->profile)){

            if(Storage::disk('public')->exists($veterinarian->profile)){
                Storage::disk('public')->delete($veterinarian->profile);
            }
        }
    }

    /**
     * Handle the Veterinarian "restored" event.
     */
    public function restored(Veterinarian $veterinarian): void
    {
        //
    }

    /**
     * Handle the Veterinarian "force deleted" event.
     */
    public function forceDeleted(Veterinarian $veterinarian): void
    {
        //
    }
}
