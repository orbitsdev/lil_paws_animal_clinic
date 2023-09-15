<?php

namespace App\Observers;

use App\Models\Monitor;
use Illuminate\Support\Facades\Storage;

class MonitorObserver
{
    /**
     * Handle the Monitor "created" event.
     */
    public function created(Monitor $monitor): void
    {
        //
    }

    /**
     * Handle the Monitor "updated" event.
     */
    public function updated(Monitor $monitor): void
    {
        //
    }

    /**
     * Handle the Monitor "deleted" event.
     */
    public function deleted(Monitor $monitor): void
    {
        
        if(!empty($monitor->monitor_image)){

            if(Storage::disk('public')->exists($monitor->monitor_image)){
                Storage::disk('public')->delete($monitor->monitor_image);
            }
        }
    }

    /**
     * Handle the Monitor "restored" event.
     */
    public function restored(Monitor $monitor): void
    {
        //
    }

    /**
     * Handle the Monitor "force deleted" event.
     */
    public function forceDeleted(Monitor $monitor): void
    {
        //
    }
}
