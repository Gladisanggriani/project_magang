<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Show login page.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle login request.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required'],
        ]);

        $credentials = [
            'username' => $request->username,
            'password' => $request->password,
        ];

        if (Auth::attempt($credentials, $request->boolean('remember'))) {

            $request->session()->regenerate();

            return redirect()->intended('/dashboard');
        }

        return back()
            ->withErrors([
                'username' => 'Username atau password salah.',
            ])
            ->onlyInput('username');
    }

    /**
     * Logout.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
