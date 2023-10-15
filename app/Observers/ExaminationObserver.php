<?php

namespace App\Observers;

use App\Models\Examination;
use Illuminate\Support\Facades\Storage;

class ExaminationObserver
{
    /**
     * Handle the Examination "created" event.
     */
    public function created(Examination $examination): void
    {
        //
    }

    /**
     * Handle the Examination "updated" event.
     */
    public function updated(Examination $examination): void
    {
        //
    }

    /**
     * Handle the Examination "deleted" event.
     */
    public function deleted(Examination $examination): void

    {

        if(!empty($examination->image_result)){

            if(Storage::disk('public')->exists($examination->image_result)){
                Storage::disk('public')->delete($examination->image_result);
            }
        }
        $examination->prescriptions()->delete();
        $examination->treatments()->delete();
    }

    /**
     * Handle the Examination "restored" event.
     */
    public function restored(Examination $examination): void
    {
        //
    }

    /**
     * Handle the Examination "force deleted" event.
     */
    public function forceDeleted(Examination $examination): void
    {
        //
    }
}
