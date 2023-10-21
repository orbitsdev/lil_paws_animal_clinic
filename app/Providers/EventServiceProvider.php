<?php

namespace App\Providers;

use App\Models\User;
use App\Models\Animal;
use App\Models\Clinic;
use App\Models\Monitor;
use App\Models\Patient;
use App\Models\Service;
use App\Models\Admission;
use App\Models\Appointment;
use App\Models\Examination;
use App\Models\Veterinarian;
use App\Models\TreatmentPlan;
use App\Models\ClinicServices;
use App\Models\AllowedCategory;
use App\Models\RequestAccess;
use App\Observers\UserObserver;
use App\Observers\AnimalObserver;
use App\Observers\ClinicObserver;
use App\Observers\MonitorObserver;
use App\Observers\PatientObserver;
use App\Observers\ServiceObserver;
use App\Observers\AdmissionObserver;
use Illuminate\Support\Facades\Event;
use App\Observers\AppointmentObserver;
use App\Observers\ExaminationObserver;
use Illuminate\Auth\Events\Registered;
use App\Observers\VeterinarianObserver;
use App\Observers\TreatmentPlanObserver;
use App\Observers\AllowedCategoryObserver;
use App\Observers\ClinicServicesObserver;
use App\Observers\RequestAccessObserver;
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
        Service::observe(ServiceObserver::class);
        Examination::observe(ExaminationObserver::class);
        Admission::observe(AdmissionObserver::class);
        Monitor::observe(MonitorObserver::class);
        TreatmentPlan::observe(TreatmentPlanObserver::class);
        AllowedCategory::observe(AllowedCategoryObserver::class);
        ClinicServices::observe(ClinicServicesObserver::class);
        RequestAccess::observe(RequestAccessObserver::class);
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
