<?php

namespace Modules\AdminAuth\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CustomerAuth
{
    /**
     * Handle an incoming request.
     * Only allows users with profile_type 'customer' to access customer panel.
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

        if ($user->profile_type !== 'customer') {
            return redirect('/')->withErrors(['email' => 'Access denied. Customer panel is restricted.']);
        }

        return $next($request);
    }
}
