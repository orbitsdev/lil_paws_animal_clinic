<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Animal;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Auth;

class AnimalPolicy
{
    // /**
    //  * Determine whether the user can view any models.
    //  */
    public function viewAny(User $user): bool
    {
        return true;
    }

    // /**
    //  * Determine whether the user can view the model.
    //  */
    // public function view(User $user, Animal $animal): bool
    // {
    //     //
    // }

    // /**
    //  * Determine whether the user can create models.
    //  */
    // public function create(User $user): bool
    // {
    //     //
    // }

    // /**
    //  * Determine whether the user can update the model.
    //  */
    // public function update(User $user, Animal $animal): bool
    // {
    //     //
    // }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Animal $animal): bool
    {   
        if ($user->hasAnyRole(['Client'])) {
            if ($user->id == $animal->user_id) {
                // Check if the animal has patients
                if (count($animal->patients) > 0) {
                    return false; // Deny delete if there are patients
                } else {
                    return true; // Allow delete if there are no patients
                }
            } else {
                return false; // Deny delete if the user doesn't own the animal
            }
        }

        if ($user->hasAnyRole(['Admin'])) {
            return true;
        }
    
        return false; // Deny delete for users without the 'Client' role
    }
    
    

    /**
     * Determine whether the user can restore the model.
     */
    // public function restore(User $user, Animal $animal): bool
    // {
    //     //
    // }

    /**
     * Determine whether the user can permanently delete the model.
     */
    // public function forceDelete(User $user, Animal $animal): bool
    // {
    //     //
    // }
}
