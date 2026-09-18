<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\Profile\ChangeAvatarRequest;
use App\Http\Requests\User\Profile\SaveRequest;
use App\Http\Resources\Orders\Client\OrderPublicResource;
use App\Http\Resources\Users\UserProfileResource;
use App\Models\Order;
use App\Services\Image\Service;
use Exception;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\LaravelData\Exceptions\InvalidDataClass;

class ProfileController extends Controller
{
    public function __construct(private readonly Service $imageService)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $client = $request->user()->load([
            'phone',
            'pendingPhones' => fn($query) => $query->orderByDesc('id'),
            'image',
        ]);

        return Inertia::render('Profile/Index', [
            'client' => fn() => UserProfileResource::from($client),
            'orders' => fn() => OrderPublicResource::collect(Order::withCount('items')->byUserId($client->id)->orderByDesc('id')->get()),
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
        return $checkSave
            ? redirect()->back()
            : redirect()->back()->withErrors(['image' => 'Не удалось загрузить файл попробуйте позднее.']);
    }

    /**
     * @throws Exception
     */
    public function destroyAvatar(Request $request)
    {
        $client = $request->user()->loadMissing('image');
        $checkDelete = $this->imageService->deleteOneImage($client);
        return $checkDelete
            ? redirect()->back()
            : redirect()->back()->withErrors(['image' => 'Произошла ошибка попробуйте позднее.']);
    }
}
