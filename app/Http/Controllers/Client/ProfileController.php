<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\Profile\SaveRequest;
use App\Http\Resources\Orders\Client\OrderPublicResource;
use App\Http\Resources\Users\UserProfileResource;
use App\Models\Order;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $client = $request->user()->load(['phone', 'pendingPhones']);

        return Inertia::render('Profile/Index', [
            'client' => fn () => UserProfileResource::from($client),
            'orders' => fn () => OrderPublicResource::collect(Order::withCount('items')->byUserId($client->id)->orderByDesc('id')->get()),
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
}
