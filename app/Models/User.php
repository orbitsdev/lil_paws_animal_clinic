<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Panel;
use App\Models\Role;
use App\Models\Animal;
use App\Models\Clinic;
use App\Models\Appointment;
use App\Models\Veterinarian;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Filament\Models\Contracts\HasName;

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
         return  $this->role()->whereIn('name', $roles)->exists();
       
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
    public function veteremaroanAppointment(){
        return $this->hasOne(Appointment::class);
    }


}
