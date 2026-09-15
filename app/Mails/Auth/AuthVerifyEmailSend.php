<?php

namespace App\Mails\Auth;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class AuthVerifyEmailSend extends VerifyEmail implements ShouldQueue
{
    use Queueable;
}
