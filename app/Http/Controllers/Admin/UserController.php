<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\RequestDTO\User\Admin\UsersQuery;
use App\Http\Requests\Admin\User\ChangeRolesRequest;
use App\Http\Resources\General\GeneralPagination;
use App\Http\Resources\Role\RoleCrudResource;
use App\Http\Resources\Users\UserCrudResource;
use App\Interfaces\SystemHelperInterface;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class UserController extends Controller
{
    public function __construct(public SystemHelperInterface $systemHelper)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $rolesForSelect = RoleCrudResource::collect(Role::get());
        $filters = UsersQuery::validateAndCreate($request->query())->toArray();
        $users = function () use ($filters) {
            $usersPaginator = QueryBuilder::for(User::class)
                ->with('roles')
                ->allowedFilters([
                    'address',
                    'phone',
                    AllowedFilter::callback(
                        'name',
                        fn($q, $v) => $q->whereAny(
                            ['first_name', 'middle_name', 'last_name'],
                            'ILIKE',
                            '%' . $v . '%'
                        )
                    ),
                ])
                ->allowedSorts(['id', 'created_at', 'block'])
                ->paginate($filters['batch'] ?? 10);

            return GeneralPagination::fromPaginator($usersPaginator, UserCrudResource::class);
        };
        return Inertia::render('Admin/Users/Index', [
            'rolesForSelect' => $rolesForSelect,
            'users' => $users,
            'query' => $filters,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ChangeRolesRequest $request, User $user)
    {
        $data = $request->getData()->toArray();
        $user->roles()->sync($data);
        $this->systemHelper->saveRolesUserInCache($user);
        return redirect()->back();
    }

}
