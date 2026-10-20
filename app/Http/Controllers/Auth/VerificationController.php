<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;

class VerificationController extends Controller
{
    public function repeatSendMail(Request $request)
    {
        $request->user()->sendEmailVerificationNotification();

        Inertia::flash(config('app-notifications.auth.verify-mail'));

        return redirect()->route('posts.index');
    }

    public function verify(EmailVerificationRequest $request)
    {
        $request->fulfill();

        Inertia::flash(config('app-notifications.auth.verify'));

        return redirect()->route('posts.index');
    }
}
