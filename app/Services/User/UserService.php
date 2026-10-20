<?php

namespace App\Services\User;

use App\Helpers\Spattie\CustomSort\SortByFieldRelation;
use App\Interfaces\SystemHelperInterface;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class UserService
{
    public function __construct(
        private readonly SystemHelperInterface $systemHelper,
    ) {}

    public function getUsersWithPaginate(array $filters): LengthAwarePaginator
    {
        return QueryBuilder::for(User::class)
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
                                fn ($query) => $query->whereAny(
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
    }

    public function changeRoles(User $user, array $roleIds): void
    {
        $user->roles()->sync($roleIds);
        $this->systemHelper->saveRolesUserInCache($user);
    }

    public function changeBlock(User $user, array $data): void
    {
        $user->block = $data['block'];
        $user->save();
    }

    public function changeAddress(User $user, string $address): void
    {
        $user->update(['address' => $address]);
    }
}
