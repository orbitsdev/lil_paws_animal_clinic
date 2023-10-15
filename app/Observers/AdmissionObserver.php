<?php

namespace App\Observers;

use App\Models\Admission;

class AdmissionObserver
{
    /**
     * Handle the Admission "created" event.
     */
    public function created(Admission $admission): void
    {
        //
    }

    /**
     * Handle the Admission "updated" event.
     */
    public function updated(Admission $admission): void
    {
        //
    }

    /**
     * Handle the Admission "deleted" event.
     */
    public function deleted(Admission $admission): void
    {
        $admission->treatmentplans->each(function ($treatmentPlan) {
            $treatmentPlan->monitors()->delete();
            $treatmentPlan->delete();
        });
    }

    /**
     * Handle the Admission "restored" event.
     */
    public function restored(Admission $admission): void
    {
        //
    }

    /**
     * Handle the Admission "force deleted" event.
     */
    public function forceDeleted(Admission $admission): void
    {
        //
    }
}
