<?php

namespace App\Http\Controllers\Auth;

use App\Enums\System\Roles;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\Register as RegisterRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

class RegisterController extends Controller
{
    public function create(): Response
    {
        return Inertia::render('Auth/Register');
    }

    public function store(RegisterRequest $request): RedirectResponse
    {
        $data = $request->getData()->toArray();
        $data['password'] = Hash::make($data['password']);
        $user = User::create($data);
        $roles = Role::whereIn('name', [Roles::USER])->pluck('id')->toArray();
        $user->roles()->sync($roles);
        $request->session()->regenerate();
        Auth::login($user);
        // TODO: отправить письмо на почту
        // TODO: отправить смс если ввели номер
        return redirect()->route('posts.index')->with('notification', 'auth.register');
    }
}
