<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Spattie\CustomSort\SortByFieldRelation;
use App\Http\Controllers\Controller;
use App\Http\RequestDTO\User\Admin\UsersQuery;
use App\Http\Requests\Admin\User\ChangeBlockRequest;
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
use Spatie\QueryBuilder\AllowedSort;
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
                ->with(['roles', 'phone'])
                ->allowedFilters([
                    'address',
                    AllowedFilter::partial('phone', 'phone.phone'),
                    AllowedFilter::callback(
                        'name',
                        function ($q, $v) {
                            $words = preg_split('/\s+/', trim($v));

                            foreach ($words as $word) {
                                $q->where(
                                    fn($query) => $query->whereAny(
                                        ['first_name', 'middle_name', 'last_name'],
                                        'ILIKE',
                                        "%$word%"
                                    )
                                );
                            }
                        }
                    ),
                ])
                ->defaultSort('-id')
                ->allowedSorts([
                    'id',
                    'created_at',
                    'block',
                    AllowedSort::custom('phone', new SortByFieldRelation('phone'), 'phone'),
                ])
                ->paginate($filters['batch'] ?? 10);
            return GeneralPagination::fromPaginator($usersPaginator, UserCrudResource::class);
        };

        return Inertia::render('Admin/Users/Index', [
            'rolesForSelect' => fn() => $rolesForSelect,
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
        $user->roles()->sync($data['roleIds']);
        $this->systemHelper->saveRolesUserInCache($user);

        return redirect()->back();
    }

    public function changeBlock(ChangeBlockRequest $request, User $user)
    {
        $data = $request->getData()->toArray();
        $user->block = $data['block'];
        $user->save();

        return redirect()->back();
    }
}
