<?php

namespace App\Observers;

use App\Models\Animal;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AnimalObserver
{
    /**
     * Handle the Animal "created" event.
     */
    public function created(Animal $animal): void
    {   

        $user = Auth::user();
        if ($user) {
            $animal->user_id = $user->id;
            $animal->save();
        }
    }

    /**
     * Handle the Animal "updated" event.
     */
    public function updated(Animal $animal): void
    {
        //
    }

    /**
     * Handle the Animal "deleted" event.
     */
    public function deleted(Animal $animal): void
    {
        if(!empty($animal->image)){

            if(Storage::disk('public')->exists($animal->image)){
                Storage::disk('public')->delete($animal->image);
            }
        }
    }

    /**
     * Handle the Animal "restored" event.
     */
    public function restored(Animal $animal): void
    {
        //
    }

    /**
     * Handle the Animal "force deleted" event.
     */
    public function forceDeleted(Animal $animal): void
    {
        //
    }
}
