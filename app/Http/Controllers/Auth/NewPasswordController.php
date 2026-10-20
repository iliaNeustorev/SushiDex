<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\PasswordReset as PasswordResetRequest;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Inertia\Inertia;

class NewPasswordController extends Controller
{
    public function create(string $token)
    {
        return Inertia::render('Auth/ResetPassword', ['token' => $token]);
    }

    public function store(PasswordResetRequest $request)
    {
        $data = $request->getData()->toArray();
        $status = Password::reset(
            $data,
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            Inertia::flash(config('app-notifications.password.reset'));

            return redirect()->route('login');
        }

        return back()->withErrors(['email' => [__($status)]]);
    }
}
