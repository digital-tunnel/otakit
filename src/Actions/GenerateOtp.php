<?php

declare(strict_types=1);

namespace DigitalTunnel\Otakit\Actions;

use DigitalTunnel\Otakit\Events\OtpGenerated;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class GenerateOtp
{
    /**
     * Handle the action.
     */
    public function handle(Model $otpable, int $length = 6, int $ttl = 5): int
    {
        $otp = rand(pow(10, $length - 1), pow(10, $length) - 1);

        $expiresAt = now()->addMinutes($ttl);

        Cache::put(
            key: 'otp.'.$otpable->getQualifiedKeyName().'.'.$otpable->id,
            value: $otp,
            ttl: $expiresAt
        );

        event(new OtpGenerated($otpable, $otp));

        return $otp;
    }
}
