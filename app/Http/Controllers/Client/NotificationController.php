<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\RequestDTO\Notifications\NotificationsQuery;
use App\Http\Requests\Notifications\UpdateRequest;
use App\Http\Resources\Notifications\DatabaseNotificationResource;
use App\Services\User\UserService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class NotificationController extends Controller
{
    public function __construct(
        public UserService $userService
    ) {}

    public function index(Request $request)
    {
        $query = NotificationsQuery::validateAndCreate($request->query())->toArray();
        $user = $request->user();

        $notificationsPaginate = $this->userService->getNotifications($user, $query);

        $notifications = DatabaseNotificationResource::collect($notificationsPaginate->items());
        $page = $notificationsPaginate->currentPage();
        $lastPage = $notificationsPaginate->lastPage();

        return Inertia::render('Notifications/Index', [
            'notifications' => Inertia::merge($notifications),
            'query' => $query,
            'page' => $page,
            'lastPage' => $lastPage,
        ]);
    }

    public function update(UpdateRequest $request, string $notification)
    {
        $query = $request->getData()->toArray();
        $user = $request->user();
        $route = $this->userService->readNotification($user, $notification);
        if (isset($query['redirect'], $route) && $query['redirect']) {
            return redirect()->to($route);
        }

        return redirect()->back();
    }

    public function readAll(Request $request)
    {
        $user = $request->user();

        $this->userService->readAllNotifications($user);

        Inertia::flash(config('app-notifications.notifications.readAll'));

        return redirect()->back();
    }
}
