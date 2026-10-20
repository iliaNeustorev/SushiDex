<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Inertia\Inertia;

class PasswordResetController extends Controller
{
    public function create()
    {
        return Inertia::render('Auth/ForgotPassword', []);
    }

    public function store(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            Inertia::flash(config('app-notifications.password.reset.email'));

            return redirect()->route('login');
        } else {
            return back()->withErrors(['email' => __($status)]);
        }
    }
}
