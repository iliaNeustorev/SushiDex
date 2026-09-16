<?php

namespace Tests\Feature;

use App\Exceptions\Phone\SmsCodeConfirmedFailed;
use App\Exceptions\Phone\SmsRateLimitExceeded;
use App\Models\PendingPhone;
use App\Models\User;
use App\Services\Phone\PhoneService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PhoneServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_confirming_phone_removes_all_matching_pending_records(): void
    {
        $client = User::factory()->create();
        $anotherClient = User::factory()->create();
        $phone = '79991234567';

        $pendingPhone = $this->pendingPhone($client, $phone, '123456');
        $duplicatePendingPhone = $this->pendingPhone($anotherClient, $phone, '654321');
        $unrelatedPendingPhone = $this->pendingPhone($anotherClient, '79997654321', '111111');

        app(PhoneService::class)->confirmPhone($client, $pendingPhone, '123456');

        $this->assertDatabaseHas('phones', [
            'user_id' => $client->id,
            'phone' => $phone,
        ]);
        $this->assertDatabaseMissing('pending_phones', ['id' => $pendingPhone->id]);
        $this->assertDatabaseMissing('pending_phones', ['id' => $duplicatePendingPhone->id]);
        $this->assertDatabaseHas('pending_phones', ['id' => $unrelatedPendingPhone->id]);
    }

    public function test_failed_confirmation_keeps_all_matching_pending_records(): void
    {
        $client = User::factory()->create();
        $anotherClient = User::factory()->create();
        $phone = '79991234567';

        $pendingPhone = $this->pendingPhone($client, $phone, '123456');
        $duplicatePendingPhone = $this->pendingPhone($anotherClient, $phone, '654321');

        try {
            app(PhoneService::class)->confirmPhone($client, $pendingPhone, '000000');
            $this->fail('Confirmation with an invalid code should fail.');
        } catch (SmsCodeConfirmedFailed) {
            // Expected exception.
        }

        $this->assertDatabaseMissing('phones', [
            'user_id' => $client->id,
            'phone' => $phone,
        ]);
        $this->assertDatabaseHas('pending_phones', ['id' => $pendingPhone->id]);
        $this->assertDatabaseHas('pending_phones', ['id' => $duplicatePendingPhone->id]);
    }

    public function test_sending_code_is_limited_per_pending_phone(): void
    {
        $client = User::factory()->create();
        $firstPendingPhone = $this->pendingPhone($client, '79991234567');
        $secondPendingPhone = $this->pendingPhone($client, '79997654321');
        $service = app(PhoneService::class);

        $service->sendVerificationCode($client, $firstPendingPhone);
        $service->sendVerificationCode($client, $secondPendingPhone);

        $this->assertNotNull($firstPendingPhone->fresh()->code_hash);
        $this->assertNotNull($secondPendingPhone->fresh()->code_hash);

        $this->expectException(SmsRateLimitExceeded::class);
        $service->sendVerificationCode($client, $firstPendingPhone);
    }

    private function pendingPhone(User $user, string $phone, ?string $code = null): PendingPhone
    {
        return $user->pendingPhones()->create([
            'phone' => $phone,
            'code_hash' => $code === null ? null : Hash::make($code),
            'code_expires_at' => $code === null ? null : now()->addMinutes(10),
        ]);
    }
}
