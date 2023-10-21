<?php
namespace App\Filament\Pages\Auth;

use Filament\Forms;
use App\Models\Role;
use Filament\Forms\Form;
use Filament\Facades\Filament;
use Filament\Pages\Auth\Register;
use Illuminate\Auth\Events\Registered;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Http\Responses\Auth\Contracts\RegistrationResponse;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;

class ClientRegister extends Register {


    public function form(Form $form): Form{

        return $form->schema([
            TextInput::make('first_name')
            ->required(),
            TextInput::make('last_name')
            ->required(),
        
            TextInput::make('phone_number')
                    
            ->minLength(11)
            ->maxLength(11)
            ->required(),
            TextInput::make('address')->required(),
            // $this->getNameFormComponent(),
            $this->getEmailFormComponent(),
            $this->getPasswordFormComponent(),
            $this->getPasswordConfirmationFormComponent(),
        ])->statePath('data');
    }

    public function register(): ?RegistrationResponse
    {
        try {
            $this->rateLimit(2);
        } catch (TooManyRequestsException $exception) {
            Notification::make()
                ->title(__('filament-panels::pages/auth/register.notifications.throttled.title', [
                    'seconds' => $exception->secondsUntilAvailable,
                    'minutes' => ceil($exception->secondsUntilAvailable / 60),
                ]))
                ->body(array_key_exists('body', __('filament-panels::pages/auth/register.notifications.throttled') ?: []) ? __('filament-panels::pages/auth/register.notifications.throttled.body', [
                    'seconds' => $exception->secondsUntilAvailable,
                    'minutes' => ceil($exception->secondsUntilAvailable / 60),
                ]) : null)
                ->danger()
                ->send();

            return null;
        }

        $data = $this->form->getState();
        $client_role = Role::whereName('Client')->first();
        $data['role_id']= $client_role->id;
        

        $user = $this->getUserModel()::create($data);

        app()->bind(
            \Illuminate\Auth\Listeners\SendEmailVerificationNotification::class,
            \Filament\Listeners\Auth\SendEmailVerificationNotification::class,
        );
        event(new Registered($user));

        Filament::auth()->login($user);

        session()->regenerate();

        return app(RegistrationResponse::class);
    }
    
}
