<?php

namespace App\Services\Phone;

use App\Exceptions\Phone\SmsCodeConfirmedFailed;
use App\Exceptions\Phone\SmsCodeExpiredFailed;
use App\Exceptions\Phone\SmsRateLimitExceeded;
use App\Exceptions\Phone\SmsSendingFailed;
use App\Interfaces\SmsSendInterface;
use App\Models\PendingPhone;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Throwable;

class PhoneService
{
    public function __construct(private SmsSendInterface $smsSendService) {}

    public function sendVerificationCode(User $client, PendingPhone $pendingPhone): void
    {
        $limiterKey = "send-sms:{$client->id}:{$pendingPhone->id}";

        $executed = RateLimiter::attempt(
            $limiterKey,
            1,
            function () use ($pendingPhone) {
                $code = random_int(100000, 999999);
                $resultSend = $this->smsSendService->sendCode($pendingPhone->phone, $code);
                if (! $resultSend) {
                    throw new SmsSendingFailed(
                        'SMS provider failed to send the verification code.',
                    );
                }
                $pendingPhone->update([
                    'code_hash' => Hash::make($code),
                    'code_expires_at' => Carbon::now()->addMinutes(10),
                ]);
            }
        );
        if (! $executed) {
            $seconds = RateLimiter::availableIn($limiterKey);
            throw new SmsRateLimitExceeded($seconds);
        }
    }

    /**
     * @throws Throwable
     */
    public function confirmPhone(User $client, PendingPhone $pendingPhone, string $code): void
    {
        $limiterKey = "confirm-sms:{$client->id}:{$pendingPhone->id}";

        if (RateLimiter::tooManyAttempts($limiterKey, 7)) {
            throw new SmsRateLimitExceeded(
                RateLimiter::availableIn($limiterKey),
            );
        }
        if (! $pendingPhone->code_hash ||
            ! $pendingPhone->code_expires_at ||
            $pendingPhone->code_expires_at->isPast()
        ) {
            throw new SmsCodeExpiredFailed;
        }
        if (! Hash::check($code, $pendingPhone->code_hash)) {
            RateLimiter::hit($limiterKey);

            throw new SmsCodeConfirmedFailed;
        }
        DB::transaction(function () use ($pendingPhone, $client) {
            $client->phone()->updateOrCreate([], [
                'phone' => $pendingPhone->phone,
                'verified_at' => Carbon::now(),
            ]);
            PendingPhone::where('phone', $pendingPhone->phone)->delete();
        });
        RateLimiter::clear($limiterKey);
    }
}
