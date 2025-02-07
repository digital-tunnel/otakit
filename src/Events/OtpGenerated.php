<?php

declare(strict_types=1);

namespace DigitalTunnel\Otakit\Events;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Events\Dispatchable;

class OtpGenerated
{
    use Dispatchable;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public Model $otpable,
        public int $otp,
    ) {}
}
