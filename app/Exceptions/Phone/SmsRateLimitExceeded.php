<?php

namespace App\Exceptions\Phone;

use RuntimeException;

class SmsRateLimitExceeded extends RuntimeException
{
    /**
     * Create a new class instance.
     */
    public function __construct(public readonly int $retryAfter)
    {
        parent::__construct('SMS rate limit exceeded.');
    }
}
