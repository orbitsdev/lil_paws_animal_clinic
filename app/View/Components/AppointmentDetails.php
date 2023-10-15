<?php

namespace App\View\Components;

use Closure;
use App\Models\Appointment;
use Illuminate\View\Component;
use Illuminate\Contracts\View\View;

class AppointmentDetails extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(public Appointment $record)
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.appointment-details');
    }
}
