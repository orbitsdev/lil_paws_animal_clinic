<?php

namespace App\Observers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {   

       
        // if(auth()->user()->hasAnyRole(['Veterenarian'])){
            
        //     $client_role = Role::whereName('Client')->first();
        //     $user->role_id = $client_role->id;
        //     $user->save();
        // }
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {   


        // if($user->hasAnyRole(['Admin', 'Client'])){
        //     if($user->clinic){
        //         $user->clinic_id = null;
        //         $user->save();
        //     }
        // }
        // if(!$user->hasAnyRole(['Vet'])){
        //     if(!empty($user->clinic)){
        //         $user->clinic_id = null;
        //         $user->save();
        //     }
        // }
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        if(!empty($user->profile)){

            if(Storage::disk('public')->exists($user->profile)){
                Storage::disk('public')->delete($user->profile);
            }
        }
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        //
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        //
    }
}
