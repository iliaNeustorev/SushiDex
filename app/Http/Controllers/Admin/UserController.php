<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\RequestDTO\User\Admin\UsersQuery;
use App\Http\Requests\Admin\User\ChangeBlockRequest;
use App\Http\Requests\Admin\User\ChangeRolesRequest;
use App\Http\Requests\User\Profile\ChangeAddressRequest;
use App\Http\Resources\General\GeneralPagination;
use App\Http\Resources\Role\RoleCrudResource;
use App\Http\Resources\Users\UserCrudResource;
use App\Models\Role;
use App\Models\User;
use App\Services\User\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class UserController extends Controller
{
    public function __construct(
        private readonly UserService $userService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $rolesForSelect = RoleCrudResource::collect(Role::get());
        $query = UsersQuery::validateAndCreate($request->query())->toArray();

        $usersPaginator = $this->userService->getUsersWithPaginate($query);
        $users = GeneralPagination::fromPaginator($usersPaginator, UserCrudResource::class);

        return Inertia::render('Admin/Users/Index', [
            'rolesForSelect' => fn () => $rolesForSelect,
            'users' => $users,
            'query' => $query,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ChangeRolesRequest $request, User $user)
    {
        $data = $request->getData()->toArray();

        $this->userService->changeRoles($user, $data['roleIds']);

        return Inertia::flash(config('app-notifications.users.admin.change-roles'))->back();
    }

    public function changeBlock(ChangeBlockRequest $request, User $user)
    {
        $data = $request->getData()->toArray();

        $this->userService->changeBlock($user, $data);

        return $data['block']
            ? Inertia::flash(config('app-notifications.users.admin.block.on'))->back()
            : Inertia::flash(config('app-notifications.users.admin.block.off'))->back();
    }

    public function changeAddress(ChangeAddressRequest $request, User $user)
    {
        $address = $request->getData()->address;

        Gate::authorize('update', $user);

        $this->userService->changeAddress($user, $address);

        return Inertia::flash(config('app-notifications.users.admin.change-address'))->back();
    }

    public function authUser(Request $request, User $user)
    {
        if ($user->id === Auth::id()) {
            return redirect()->back();
        }

        $this->userService->authUser($request, $user);

        return redirect()->route('home');
    }
}
