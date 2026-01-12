<?php

namespace Modules\AdminAuth\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Modules\Support\Http\Controllers\AppController;
use Modules\User\Models\SocialAccount;
use Modules\User\Models\User;

class SocialAuthController extends AppController
{
    public function googleRedirect(Request $request)
    {
        if (! config('services.google.client_id') || ! config('services.google.client_secret') || ! config('services.google.redirect')) {
            abort(500, 'Google login is not configured.');
        }

        return Socialite::driver('google')
            ->scopes(['openid', 'email', 'profile'])
            ->redirect();
    }

    public function googleCallback(Request $request)
    {
        try {
            $socialUser = Socialite::driver('google')->user();
        } catch (\Throwable $e) {
            return redirect()->route('adminAuth.loginForm')
                ->withErrors(['email' => 'Google authentication failed. Please try again.']);
        }

        $email = $socialUser->getEmail();
        if (! $email) {
            return redirect()->route('adminAuth.loginForm')
                ->withErrors(['email' => 'Your Google account did not provide an email address.']);
        }

        $provider = 'google';
        $providerId = (string) $socialUser->getId();

        $account = SocialAccount::query()
            ->with('user')
            ->where('provider', $provider)
            ->where('provider_id', $providerId)
            ->first();

        $user = $account?->user;

        if (! $user) {
            $user = User::query()->where('email', $email)->first();
        }

        $isNewUser = false;

        if (! $user) {
            $isNewUser = true;

            $user = User::create([
                'name' => $socialUser->getName() ?: 'Google User',
                'email' => $email,
                'password' => Hash::make(Str::random(48)),
                'type' => 'tenant',
            ]);
        }

        if (method_exists($user, 'hasVerifiedEmail') && ! $user->hasVerifiedEmail()) {
            $user->forceFill(['email_verified_at' => now()])->save();
        }

        SocialAccount::updateOrCreate(
            ['provider' => $provider, 'provider_id' => $providerId],
            [
                'user_id' => $user->id,
                'email' => $email,
                'name' => $socialUser->getName(),
                'avatar' => $socialUser->getAvatar(),
            ]
        );

        Auth::guard('user')->login($user, true);
        $request->session()->regenerate();

        if ($user->type === 'user') {
            return redirect()->intended(route('superadmin.dashboard.index'));
        }

        if ($isNewUser || ! $user->tenants()->exists()) {
            return redirect()->route('register', ['onboarding' => 1]);
        }

        return redirect()->intended(route('dashboard.index'));
    }
}
