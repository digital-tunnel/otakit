<?php

declare(strict_types=1);

namespace DigitalTunnel\Otakit\Traits;

use DigitalTunnel\Otakit\Actions\GenerateOtp;
use DigitalTunnel\Otakit\Actions\ValidateOtp;

trait Otakit
{
    /**
     * Generate an OTP code for the given Otpable model
     */
    public function generateOtp(?int $length = 6, ?int $ttl = 5): int
    {
        return (new GenerateOtp)->handle(
            otpable: $this,
            length: $length ?? config('otakit.otp.length', 6),
            ttl: $ttl ?? config('otakit.otp.ttl', 5)
        );
    }

    /**
     * Validate the OTP code for the given Otpable model
     */
    public function validateOtp(int $otp): bool
    {
        return (new ValidateOtp)->handle(
            otpable: $this,
            otp: $otp
        );
    }
}
