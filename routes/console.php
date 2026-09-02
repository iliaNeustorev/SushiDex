<?php

use App\Enums\System\Roles;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('test', function () {
    $user = User::find(2);
    $roles = Role::whereIn('name', [Roles::AUTHOR, Roles::DEVELOPER, Roles::ADMIN, Roles::USER])
        ->pluck('id')
        ->toArray();
    $user->roles()->sync($roles);
})->purpose('ОК');
