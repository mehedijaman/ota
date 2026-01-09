<?php

namespace Modules\AdminAuth\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Modules\AdminAuth\Http\Requests\LoginRequest;
use Modules\Support\Http\Controllers\AppController;

class AuthenticatedSessionController extends AppController
{
    /**
     * Display the login view.
     *
     * @return \Inertia\Response
     */
    public function loginForm()
    {
        return inertia('Auth/LoginForm');
    }

    /**
     * Handle an incoming authentication request.
     *
     * @param  \Modules\AdminAuth\Http\Requests\LoginRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(LoginRequest $request)
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = auth()->user();

        return match ($user->profile_type) {
            'agent' => redirect()->intended(route('agentDashboard.index')),
            'customer' => redirect()->intended(route('site.index')),
            default => redirect()->intended(route(config('modular.default-logged-route'))),
        };
    }

    /**
     * Destroy an authenticated session.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function logout(Request $request)
    {
        Auth::guard('user')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return Inertia::location(config('modular.login-url'));
    }
}
