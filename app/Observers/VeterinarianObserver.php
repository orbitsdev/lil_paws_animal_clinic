<?php

namespace App\Observers;

use App\Models\Veterinarian;

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
        if(!empty($user->profile)){

            if(Storage::disk('public')->exists($user->profile)){
                Storage::disk('public')->delete($user->profile);
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
