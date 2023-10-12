<?php

namespace App\Filament\Clinic\Pages\Auth;

use App\Models\Role;
use App\Models\Clinic;
use Filament\Forms\Form;
use Filament\Facades\Filament;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Wizard;
use Illuminate\Auth\Events\Registered;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Forms\Components\FileUpload;
use Filament\Pages\Auth\Register as BaseRegisterPage;
use Filament\Http\Responses\Auth\Contracts\RegistrationResponse;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;

class ClinicRegister extends BaseRegisterPage
{
    public function form(Form $form): Form{

        return $form->schema([
             
Wizard::make([
    Wizard\Step::make('Account')
        ->schema([
            FileUpload::make('profile')
            ->disk('public')
            ->directory('user-profile')
            ->image()
            ->imageEditor()
            ->imageEditorAspectRatios([
                '16:9',
                '4:3',
                '1:1',
            ])
            // ->imageEditorMode(2)
          
            ->required()
            ,
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
        ]),
    Wizard\Step::make('Clinic')
        ->schema([


            FileUpload::make('valid_id')
            ->disk('public')
            ->directory('user-valid-id')
            ->image()
            ->imageEditor()
            ->imageEditorMode(2)
            ->required()
            ->label('Valid ID')
            ,
            TextInput::make('clinic_name')->label('Clinic Name')
            ->required(),
                      
            Textarea::make('clinic_address')
            ->rows(5)
            ->required()
            ,

            FileUpload::make('clinic_image')
            ->disk('public')
            ->directory('clinic')
            ->image()
            ->imageEditor()
            ->imageEditorMode(2)
            ->required()
            ->label('Business Location Image')
            ,
        ]),
   
    ]),
           

           
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
        $client_role = Role::whereName('Veterenarian')->first();
        $data['role_id']= $client_role->id;

        $modified_user_data = [
            'first_name'=> $data['first_name'],
            'last_name'=> $data['last_name'],
            'phone_number'=> $data['phone_number'],
            'address'=> $data['address'],
            'role_id'=> $data['role_id'],
            'email'=> $data['email'],
            'password'=> $data['password'],
            'password'=> $data['password'],
        ];
        $modified_clinic_data = [
            

                'name'=>  $data['clinic_name'],
                'valid_id'=> $data['valid_id'],
                'address'=> $data['clinic_address'],
                'image'=> $data['clinic_image'],

            
        ];


        $data_collection = [
            'account'=> $modified_user_data,
            'clinic'=> $modified_clinic_data,
        ];

        // $newClinic = Clinic::create([
        //    'name'=> $data['clinic_name'],
        //    'address'=> $data['business_location'],
        //    'image'=> $data['clinic_name'],
        // ]);

        

        $user = $this->getUserModel()::create($data_collection['account']);
        $newClinic = Clinic::create($data_collection['clinic']);
        $user->clinic_id = $newClinic->id;
        $newClinic->user_id = $user->id;
        $newClinic->from_request = true;

        $user->save();
        $newClinic->save();

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
