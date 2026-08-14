<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PortalAuthController extends Controller
{
    public function create(): View
    {
        return view('portal.auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate(['email' => ['required', 'email'], 'password' => ['required', 'string']]);
        if (! Auth::guard('portal')->attempt(['email' => $credentials['email'], 'password' => $credentials['password'], 'is_active' => true], $request->boolean('remember'))) {
            return back()->withErrors(['email' => 'Email atau kata sandi tidak valid.'])->onlyInput('email');
        }

        $request->session()->regenerate();
        Auth::guard('portal')->user()->update(['last_login_at' => now()]);

        return to_route('portal.dashboard');
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('portal')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return to_route('portal.login');
    }
}
