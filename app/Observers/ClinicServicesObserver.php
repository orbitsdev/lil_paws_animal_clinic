<?php

namespace App\Observers;

use App\Models\ClinicServices;
use Illuminate\Support\Facades\Auth;

class ClinicServicesObserver
{
    /**
     * Handle the ClinicServices "created" event.
     */
    public function created(ClinicServices $clinicServices): void
    {   
        $user = Auth::user();
        if ($user->hasAnyRole(['Veterenarian'])) {
            if($user->clinic){
                $clinicServices->clinic_id = $user->clinic->id;
                $clinicServices->save();
            }
            // $animal->user_id = $user->id;
            // $animal->save();
        }
    }

    /**
     * Handle the ClinicServices "updated" event.
     */
    public function updated(ClinicServices $clinicServices): void
    {
        //
    }

    /**
     * Handle the ClinicServices "deleted" event.
     */
    public function deleted(ClinicServices $clinicServices): void
    {
        $clinicServices->allowedCategories()->detach();
    }

    /**
     * Handle the ClinicServices "restored" event.
     */
    public function restored(ClinicServices $clinicServices): void
    {
        //
    }

    /**
     * Handle the ClinicServices "force deleted" event.
     */
    public function forceDeleted(ClinicServices $clinicServices): void
    {
        //
    }
}
