<?php

namespace Modules\AdminAuth\Http\Controllers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Modules\Customer\Models\Customer;
use Modules\Support\Http\Controllers\SiteController;
use Modules\User\Models\User;

class RegisterController extends SiteController
{
    /**
     * Display the registration form.
     *
     * @return \Inertia\Response
     */
    public function registerForm()
    {
        return Inertia::render('Auth/RegisterForm');
    }

    /**
     * Handle an incoming registration request.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function register(Request $request)
    {
        $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users'],
            'phone' => ['required', 'string', 'max:20'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Create customer record
        $customer = Customer::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
        ]);

        // Create user record linked to customer
        $user = User::create([
            'name' => $request->first_name.' '.$request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'profile_type' => 'customer',
            'profile_id' => $customer->id,
        ]);

        event(new Registered($user));

        // Log the user in so they can access the verification page
        Auth::guard('user')->login($user);

        return redirect()->route('verification.notice')
            ->with('status', 'Registration successful! Please check your email to verify your account.');
    }
}
