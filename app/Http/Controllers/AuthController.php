<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Zobrazit přihlašovací formulář
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Zpracovat přihlášení
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            
            return redirect()->intended(route('dashboard'))
                ->with('success', 'Úspěšně jste se přihlásili!');
        }

        throw ValidationException::withMessages([
            'email' => 'Nesprávné přihlašovací údaje.',
        ]);
    }

    /**
     * Odhlášení uživatele
     */
    public function logout(Request $request)
    {
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('login')
            ->with('success', 'Byli jste úspěšně odhlášeni.');
    }
}
