<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Panel;
use App\Models\Role;
use App\Models\Animal;
use App\Models\Clinic;
use App\Models\Patient;
use App\Models\Admission;
use App\Models\Appointment;
use App\Models\Veterinarian;
use Laravel\Sanctum\HasApiTokens;
use Filament\Models\Contracts\HasName;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements FilamentUser, HasName
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'phone_number',
        'address',
        'email',
        'password',
        'role_id',
        'clinic_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function role(){
        return $this->belongsTo(Role::class);
    }

    public function veterinarian(){
        return $this->hasOne(Veterinarian::class);
    }

public function hasAnyRole($roles) {

    if($this->role()->whereIn('name', $roles)->first()){
            return true;
    }
    return false;

       
    }
   
    public function canAccessPanel(Panel $panel): bool
    {
        
        return match($panel->getId()){
            'admin'=> $this->hasAnyRole(['Admin']),
            'clinic'=> $this->hasAnyRole(['Admin','Veterenarian']),
            'client'=> $this->hasAnyRole(['Admin','Client','Veterenarian']),
        };
    }

    public function animals() {
        return $this->hasMany(Animal::class);
    }
    public function animal() {
        return $this->hasOne(Animal::class);
    }

    public function appointments(){
        return $this->hasMany(Appointment::class);
    }

    public function getFilamentName(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function clinic(){
        return $this->belongsTo(Clinic::class);
    }

    public function appointment(){
        return $this->hasOne(Appointment::class,'user_id');
    }
    public function veterenarianAppointment(){
        return $this->hasOne(Appointment::class,'veterinarian_id');
    }
    public function veteremaroanAppointment(){
        return $this->hasOne(Appointment::class);
    }

    public function veterenarianPatients(){
        return $this->hasMany(Patient::class,'veterinarian_id');
    }
    public function veterenarianPatient(){
        return $this->hasOne(Patient::class,'veterinarian_id');
    }

    public function admissionVeterenarian(){
        return $this->hasMany(Admission::class,'veterinarian_id');
    }

    // public function ownerclinic(){
    //     return $this->hasOne(Clinic::class, 'user_id');
    // }
    public function ownedClinic()
    {
        return $this->hasOne(Clinic::class, 'user_id', 'id');
    }

}
