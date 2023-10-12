<?php

namespace App\Http\Controllers;

use App\Filament\Resources\ClinicResource;
use Illuminate\Http\Request;
use Filament\Facades\Filament;
use Filament\Http\Controllers\Auth\LogoutController as BaseLogoutController;

class LogoutController extends BaseLogoutController
{
    public function logout(Request $request)
    {
        Filament::auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/clinic/login');
    }
}
