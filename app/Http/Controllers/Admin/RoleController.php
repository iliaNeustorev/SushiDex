<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ChangedRole;
use App\Interfaces\SystemHelperInterface;
use App\Models\Role;
use App\Models\User;

class RoleController extends Controller
{
    public function __construct(public SystemHelperInterface $systemHelper) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $AllRoles = Role::get();
        $userWithRoles = User::with('roles')->pluck('id');

        return view('Role/Index', compact('userWithRoles', 'AllRoles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ChangedRole $request, User $user)
    {
        $user = $request->user();
        $user->roles()->sync($request['roles']);
        $this->systemHelper->saveRolesUserInCache($user);

        return redirect()->back();
    }
}
