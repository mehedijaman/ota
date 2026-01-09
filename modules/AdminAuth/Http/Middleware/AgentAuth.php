<?php

namespace Modules\AdminAuth\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class AgentAuth
{
    /**
     * Handle an incoming request.
     * Only allows users with profile_type 'agent' to access agent panel.
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

        if ($user->profile_type !== 'agent') {
            return redirect('/')->withErrors(['email' => 'Access denied. Agent panel is restricted.']);
        }

        return $next($request);
    }
}
