<?php

declare(strict_types=1);

namespace DigitalTunnel\Otakit\Actions;

use DigitalTunnel\Otakit\Events\OtpValidationFailed;
use DigitalTunnel\Otakit\Events\OtpValidationSuccess;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class ValidateOtp
{
    /**
     * Handle the action.
     */
    public function handle(Model $otpable, int $otp): bool
    {
        $cachedOtp = Cache::get(
            key: 'otp.'.$otpable->getQualifiedKeyName().'.'.$otpable->id
        );

        if ($cachedOtp && $cachedOtp === $otp) {

            Cache::forget(
                key: 'otp.'.$otpable->getQualifiedKeyName().'.'.$otpable->id
            );

            event(new OtpValidationSuccess(
                otpable: $otpable,
                otp: $otp
            ));

            return true;
        }

        event(new OtpValidationFailed(
            otpable: $otpable,
            otp: $otp
        ));

        return false;
    }
}
