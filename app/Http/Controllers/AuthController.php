<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request): RedirectResponse
    {
        $credentials = $request->validated() + ['is_active' => true];

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            throw ValidationException::withMessages(['email' => 'Email atau kata sandi salah, atau akun nonaktif.']);
        }

        $request->session()->regenerate();

        return redirect()->intended($request->user()->isOwner() ? route('dashboard') : route('pos'));
    }

    /**
     * Login 1-klik instan untuk demo produk langsung dari halaman landing opener.
     */
    public function demoLogin(Request $request): RedirectResponse
    {
        $role = $request->input('role', User::ROLE_OWNER);
        $email = ($role === User::ROLE_KASIR) ? 'kasir@umkm.test' : 'owner@umkm.test';

        $user = User::where('email', $email)->where('is_active', true)->first();
        if ($user) {
            Auth::login($user);
            $request->session()->regenerate();

            return redirect()->intended($user->isOwner() ? route('dashboard') : route('pos'));
        }

        return redirect()->route('login');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('landing');
    }
}
