<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'nis_nip'  => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials)) {
            throw ValidationException::withMessages([
                'nis_nip' => 'NIS/NIP atau password salah.',
            ]);
        }

        $request->session()->regenerate();

        $target = Auth::user()->role === 'guru' ? 'guru-dashboard' : 'profil';

        return redirect(route('portal.index') . '#' . $target);
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect(route('portal.index'));
    }
}