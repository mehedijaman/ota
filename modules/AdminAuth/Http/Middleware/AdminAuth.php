<?php

namespace Modules\AdminAuth\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class AdminAuth
{
    /**
     * Handle an incoming request.
     * Only allows users with profile_type 'user' or 'employee' to access admin panel.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (Auth::guard('user')->guest()) {
            return redirect()->route('auth.loginForm')
                ->withErrors(['email' => 'Session expired, please login again.']);
        }

        $user = Auth::guard('user')->user();

        if (! in_array($user->profile_type, ['user', 'employee'])) {
            return redirect('/')->withErrors(['email' => 'Access denied. Admin panel is restricted.']);
        }

        return $next($request);
    }
}
