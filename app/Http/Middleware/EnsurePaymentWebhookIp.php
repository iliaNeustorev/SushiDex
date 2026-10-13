<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\IpUtils;
use Symfony\Component\HttpFoundation\Response;

class EnsurePaymentWebhookIp
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (app()->environment('production')) {
            abort_unless(
                IpUtils::checkIp($request->ip(), config('ykassa.webhook_ip')),
                Response::HTTP_FORBIDDEN,
            );
        }

        return $next($request);
    }
}
