<?php

namespace Modules\AdminAuth\Http\Controllers;

use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\Support\Http\Controllers\SiteController;

class EmailVerificationController extends SiteController
{
    /**
     * Display the email verification notice.
     *
     * @return \Inertia\Response|\Illuminate\Http\RedirectResponse
     */
    public function notice(Request $request)
    {
        return $request->user()?->hasVerifiedEmail()
            ? redirect()->intended(route('site.index'))
            : Inertia::render('Auth/VerifyEmail');
    }

    /**
     * Mark the authenticated user's email address as verified.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function verify(EmailVerificationRequest $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(route('site.index'));
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        return redirect()->intended(route('site.index'))
            ->with('status', 'Email verified successfully!');
    }

    /**
     * Send a new email verification notification.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function send(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(route('site.index'));
        }

        $request->user()->sendEmailVerificationNotification();

        return back()->with('status', 'Verification link sent!');
    }
}
