<?php

namespace App\Observers;

use App\Models\AllowedCategory;
use Illuminate\Support\Facades\Auth;

class AllowedCategoryObserver
{
    /**
     * Handle the AllowedCategory "created" event.
     */
    public function created(AllowedCategory $allowedCategory): void
    {
        $user = Auth::user();
        if ($user->hasAnyRole(['Veterenarian'])) {
            if($user->clinic){
                $allowedCategory->clinic_id = $user->clinic->id;
                $allowedCategory->save();
            }
            // $animal->user_id = $user->id;
            // $animal->save();
        }
    }

    /**
     * Handle the AllowedCategory "updated" event.
     */
    public function updated(AllowedCategory $allowedCategory): void
    {
        //
    }

    /**
     * Handle the AllowedCategory "deleted" event.
     */
    public function deleted(AllowedCategory $allowedCategory): void
    {
        $allowedCategory->clinicServices()->detach();
    }

    /**
     * Handle the AllowedCategory "restored" event.
     */
    public function restored(AllowedCategory $allowedCategory): void
    {
        //
    }

    /**
     * Handle the AllowedCategory "force deleted" event.
     */
    public function forceDeleted(AllowedCategory $allowedCategory): void
    {
        //
    }
}
