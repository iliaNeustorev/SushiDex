<?php

namespace App\Http\Controllers\Client;

use App\Exceptions\Phone\SmsCodeConfirmedFailed;
use App\Exceptions\Phone\SmsCodeExpiredFailed;
use App\Exceptions\Phone\SmsRateLimitExceeded;
use App\Exceptions\Phone\SmsSendingFailed;
use App\Http\Controllers\Controller;
use App\Http\Requests\PendingPhone\ConfirmCodeRequest;
use App\Http\Requests\PendingPhone\SendCodeRequest;
use App\Http\Requests\PendingPhone\StoreRequest;
use App\Models\PendingPhone;
use App\Services\Phone\PhoneService;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class PhoneController extends Controller
{

    public function __construct(private readonly PhoneService $phoneService)
    {
    }

    public function store(StoreRequest $request)
    {
        $client = $request->user();
        $phone = $request->getData()->phone;
        $client->pendingPhones()->create(['phone' => $phone]);
        return redirect()->back();
    }

    public function sendCode(SendCodeRequest $request)
    {
        $client = $request->user();
        $pendingPhoneId = $request->getData()->id;
        $pendingPhone = $client->pendingPhones()->findOrFail($pendingPhoneId);
        try {
            $this->phoneService->sendVerificationCode($client, $pendingPhone);
        } catch (SmsRateLimitExceeded $exception) {
            throw ValidationException::withMessages([
                'id' => "Код уже отправлен. Повторите через {$exception->retryAfter} сек.",
            ]);
        } catch (SmsSendingFailed $exception) {
            Log::error('Ошибка отправки кода', ['errorMessage' => $exception->getMessage()]);
            throw ValidationException::withMessages([
                'id' => 'Не получилось отправить код попробуйте позднее',
            ]);
        }
        return redirect()->back();
    }

    public function confirmCode(ConfirmCodeRequest $request)
    {
        $data = $request->getData()->toArray();
        $client = $request->user();
        $pendingPhone = $client->pendingPhones()->findOrFail($data['id']);
        try {
            $this->phoneService->confirmPhone($client, $pendingPhone, $data['code']);
        } catch (SmsCodeExpiredFailed) {
            throw ValidationException::withMessages([
                'code' => 'Код устарел, необходимо запросить новый!',
            ]);
        } catch (SmsCodeConfirmedFailed) {
            throw ValidationException::withMessages([
                'code' => 'Код не совпадает с отправленным!',
            ]);
        } catch (SmsRateLimitExceeded $e) {
            throw ValidationException::withMessages([
                'code' => "Слишком много попыток ввода. Попробуйте позднее через {$e->retryAfter} сек.",
            ]);
        } catch (QueryException $exception) {
            report($exception);

            throw ValidationException::withMessages([
                'code' => 'Не удалось подтвердить телефон. Попробуйте позднее.',
            ]);
        }

        return redirect()->back();
    }

    public function destroy(Request $request, PendingPhone $pendingPhone)
    {
        $client = $request->user();
        if ($pendingPhone->user_id !== $client->id) {
            abort(403);
        }
        $pendingPhone->delete();
        return redirect()->back();
    }
}
