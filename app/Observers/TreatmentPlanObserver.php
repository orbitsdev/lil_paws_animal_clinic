<?php

namespace App\Observers;

use App\Models\TreatmentPlan;

class TreatmentPlanObserver
{
    /**
     * Handle the TreatmentPlan "created" event.
     */
    public function created(TreatmentPlan $treatmentPlan): void
    {
        //
    }

    /**
     * Handle the TreatmentPlan "updated" event.
     */
    public function updated(TreatmentPlan $treatmentPlan): void
    {
        //
    }

    /**
     * Handle the TreatmentPlan "deleted" event.
     */
    public function deleted(TreatmentPlan $treatmentPlan): void
    {
        $treatmentPlan->monitors->each(function ($monitor) {
            $monitor->delete();
        });
    }

    /**
     * Handle the TreatmentPlan "restored" event.
     */
    public function restored(TreatmentPlan $treatmentPlan): void
    {
        //
    }

    /**
     * Handle the TreatmentPlan "force deleted" event.
     */
    public function forceDeleted(TreatmentPlan $treatmentPlan): void
    {
        //
    }
}
