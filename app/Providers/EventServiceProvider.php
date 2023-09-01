<?php

namespace App\Providers;

use App\Models\User;
use App\Models\Animal;
use App\Models\Clinic;
use App\Models\Patient;
use App\Models\Appointment;
use App\Models\Veterinarian;
use App\Observers\UserObserver;
use App\Observers\AnimalObserver;
use App\Observers\ClinicObserver;
use App\Observers\PatientObserver;
use Illuminate\Support\Facades\Event;
use App\Observers\AppointmentObserver;
use Illuminate\Auth\Events\Registered;
use App\Observers\VeterinarianObserver;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        User::observe(UserObserver::class);
        Clinic::observe(ClinicObserver::class);
        Veterinarian::observe(VeterinarianObserver::class);
        Animal::observe(AnimalObserver::class);
        Appointment::observe(AppointmentObserver::class);
        Patient::observe(PatientObserver::class);
        
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
