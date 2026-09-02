<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class Roles extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Role::updateOrcreate(['name' => \App\Enums\System\Roles::DEVELOPER->value, 'description' => 'Разработчик']);
        Role::updateOrCreate(['name' => \App\Enums\System\Roles::ADMIN->value, 'description' => 'Администратор']);
        Role::updateOrCreate(['name' => \App\Enums\System\Roles::AUTHOR->value, 'description' => 'Автор']);
        Role::updateOrCreate(['name' => \App\Enums\System\Roles::USER, 'description' => 'Пользователь']);
    }
}
