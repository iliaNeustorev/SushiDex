<?php

namespace App\Services\User;

use App\Helpers\Spattie\CustomSort\SortByFieldRelation;
use App\Interfaces\SystemHelperInterface;
use App\Models\User;
use App\Notifications\Client\Profiles\UpdateAddress;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class UserService
{
    private const NOTIFICATIONS_PER_PAGE = 5;

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
        $user->notify(new UpdateAddress($address));
    }

    public function getNotifications(User $user, array $payload): LengthAwarePaginator
    {
        $notifications = ($payload['all'] ?? false)
            ? $user->notifications()
            : $user->unreadNotifications();

        return $notifications->orderByDesc('id')->paginate(self::NOTIFICATIONS_PER_PAGE);
    }

    public function readAllNotifications(User $user): void
    {
        $user->unreadNotifications()->update(['read_at' => now()]);
    }

    public function readNotification(User $user, string $id): ?string
    {
        $notification = $user->unreadNotifications()->where('id', $id)->first();
        $notification?->markAsRead();

        return $notification?->data['url'] ?? null;
    }

    public function changePassword(User $user, string $password): void
    {
        $user->forceFill([
            'password' => Hash::make($password),
            'remember_token' => Str::random(60),
        ])->save();
    }

    public function deleteProfile(Request $request, User $user): bool
    {
        $checkActualOrders = $user->orders()
            ->actualOrder($user->id)
            ->exists();
        if ($checkActualOrders) {
            Inertia::flash(['type' => 'danger', 'text' => 'Не удалось удалить профиль.Есть активные заказы!']);

            return false;
        }
        try {
            DB::beginTransaction();
            $user->pendingPhones()->delete();
            $user->notifications()->delete();
            $user->posts()->delete();
            $user->phone()->delete();
            $user->products()->detach();
            $user->image()->delete();
            $user->roles()->detach();
            $user->delete();

            DB::afterCommit(function () use ($request) {
                Auth::guard('web')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
            });

            DB::commit();

            Inertia::flash(config('app-notifications.profile.delete'));

            return true;
        } catch (\Throwable $e) {
            report($e);

            Inertia::flash(config('app-notifications.profile.delete-failed'));

            DB::rollBack();

            return false;
        }
    }

    public function authUser(Request $request, User $user): void
    {
        Auth::guard('web')->logout();
        $request->session()->flush();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        Auth::loginUsingId($user->id);
        $request->session()->regenerate();
    }
}
