<?php

namespace App\Policies;

use App\Models\Appointment;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AppointmentPolicy
{
    // /**
    //  * Determine whether the user can view any models.
    //  */
    // public function viewAny(User $user): bool
    // {
    //     //
    // }

    // /**
    //  * Determine whether the user can view the model.
    //  */
    // public function view(User $user, Appointment $appointment): bool
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
    // public function update(User $user, Appointment $appointment): bool
    // {
    
    // }

    // /**
    //  * Determine whether the user can delete the model.
    //  */
    // public function delete(User $user, Appointment $appointment): bool
    // {
    //     //
    // }

    // /**
    //  * Determine whether the user can restore the model.
    //  */
    // public function restore(User $user, Appointment $appointment): bool
    // {
    //     //
    // }

    // /**
    //  * Determine whether the user can permanently delete the model.
    //  */
    // public function forceDelete(User $user, Appointment $appointment): bool
    // {
    //     //
    // }


    public function update(User $user, Appointment $appointment): bool
    {
        if ($user->hasAnyRole(['Client']) && $appointment->hasStatus(['Accepted','Completed'])) {
        return false; // Forbid the delete when the user is a client and the appointment status is in the ['Accepted','Completed'] array
    }

    return true;
    }

    public function delete(User $user, Appointment $appointment): bool
    {
        if ($user->hasAnyRole(['Client']) && $appointment->hasStatus(['Accepted','Completed'])) {
        return false; // Forbid the delete when the user is a client and the appointment status is in the $forbiddenStatuses array
    }

    return true;
    }

}
