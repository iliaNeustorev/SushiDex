<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\Profile\ChangeAvatarRequest;
use App\Http\Requests\User\Profile\ChangePasswordRequest;
use App\Http\Requests\User\Profile\SaveRequest;
use App\Http\Resources\Orders\Client\OrderPublicResource;
use App\Http\Resources\Users\UserProfileResource;
use App\Services\Image\Service;
use App\Services\Order\OrderService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Spatie\LaravelData\Exceptions\InvalidDataClass;

class ProfileController extends Controller
{
    public function __construct(
        private readonly Service $imageService,
        private readonly OrderService $orderService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $client = $request->user()->loadMissing([
            'phone',
            'pendingPhones' => fn ($query) => $query->orderByDesc('id'),
            'image',
        ]);
        $ordersPaginator = $this->orderService->getOrdersWithPaginateForClient($client);

        $orders = OrderPublicResource::collect($ordersPaginator->items());
        $page = $ordersPaginator->currentPage();
        $lastPage = $ordersPaginator->lastPage();

        if ($page > $lastPage) {
            return redirect()->route('profile.index', ['page' => 1, 'tab' => 'orders']);
        }

        return Inertia::render('Profile/Index', [
            'client' => fn () => UserProfileResource::from($client),
            'orders' => Inertia::merge($orders),
            'page' => $page,
            'lastPage' => $lastPage,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SaveRequest $request)
    {
        $data = $request->getData()->toArray();
        $user = $request->user();
        $user->update($data);

        Inertia::flash(config('app-notifications.profile.update'));

        return redirect()->back();
    }

    /**
     * @throws InvalidDataClass
     * @throws Exception
     */
    public function changeAvatar(ChangeAvatarRequest $request)
    {
        $client = $request->user()->loadMissing('image');
        $data = $request->getData();
        $image = $data->image;
        $schema = config('app.imageables');
        $catalogPath = $schema[$data->item]['catalog'];
        $data = [
            'image' => $image,
            'catalogPath' => $catalogPath,
        ];
        $checkSave = $this->imageService->saveOneImage($client, $data);

        if ($checkSave) {
            Inertia::flash(config('app-notifications.profile.change-avatar'));
        } else {
            Inertia::flash(config('app-notifications.profile.change-avatar-fail'));
        }

        return redirect()->back();
    }

    /**
     * @throws Exception
     */
    public function destroyAvatar(Request $request)
    {
        $client = $request->user()->loadMissing('image');
        $checkDelete = $this->imageService->deleteOneImage($client);

        if ($checkDelete) {
            Inertia::flash(config('app-notifications.profile.delete-avatar'));
        } else {
            Inertia::flash(config('app-notifications.profile.change-avatar-fail'));
        }

        return redirect()->back();
    }

    public function changePassword(ChangePasswordRequest $request)
    {
        $data = $request->getData();
        $request->user()->forceFill([
            'password' => Hash::make($data->password),
            'remember_token' => Str::random(60),
        ])->save();

        Inertia::flash(config('app-notifications.profile.change-password'));

        return redirect()->back();
    }
}
