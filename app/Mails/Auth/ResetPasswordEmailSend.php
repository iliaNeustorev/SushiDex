<?php

namespace App\Mails\Auth;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ResetPasswordEmailSend extends ResetPassword implements ShouldQueue
{
    use Queueable;
}
