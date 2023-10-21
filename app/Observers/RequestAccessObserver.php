<?php

namespace App\Observers;

use App\Models\RequestAccess;
use Illuminate\Support\Facades\Auth;

class RequestAccessObserver
{
    /**
     * Handle the RequestAccess "created" event.
     */
    public function created(RequestAccess $requestAccess): void
    {
        $user = Auth::user();
        if ($user->hasAnyRole(['Veterenarian'])) {
            if($user->clinic){
                $requestAccess->from_clinic_id = $user->clinic->id;
                $requestAccess->save();
            }
            // $animal->user_id = $user->id;
            // $animal->save();
        }
    }

    /**
     * Handle the RequestAccess "updated" event.
     */
    public function updated(RequestAccess $requestAccess): void
    {
        //
    }

    /**
     * Handle the RequestAccess "deleted" event.
     */
    public function deleted(RequestAccess $requestAccess): void
    {
        //
    }

    /**
     * Handle the RequestAccess "restored" event.
     */
    public function restored(RequestAccess $requestAccess): void
    {
        //
    }

    /**
     * Handle the RequestAccess "force deleted" event.
     */
    public function forceDeleted(RequestAccess $requestAccess): void
    {
        //
    }
}
