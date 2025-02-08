<?php

declare(strict_types=1);

use DigitalTunnel\Otakit\Actions\GenerateOtp;
use DigitalTunnel\Otakit\Actions\ValidateOtp;
use DigitalTunnel\Otakit\Events\OtpGenerated;
use DigitalTunnel\Otakit\Events\OtpValidationFailed;
use DigitalTunnel\Otakit\Events\OtpValidationSuccess;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;

it('can generate OTP for otpable model', function () {
    $otpable = new class extends Model
    {
        public function getQualifiedKeyName(): string
        {
            return 'users.id';
        }
    };

    $otpable->id = 1;

    Event::fakeFor(function () use ($otpable) {

        $otp = (new GenerateOtp)->handle(
            otpable: $otpable
        );

        $cacheKey = "otp.{$otpable->getQualifiedKeyName()}.{$otpable->id}";

        Event::assertDispatched(OtpGenerated::class);

        expect(Cache::get($cacheKey))->toBe($otp);

    }, [OtpGenerated::class]);
});

it('can validate OTP with valid code', function () {
    // Mock a model
    $otpable = new class extends Model
    {
        public function getQualifiedKeyName(): string
        {
            return 'users.id';
        }
    };
    $otpable->id = 1;

    $cacheKey = "otp.{$otpable->getQualifiedKeyName()}.{$otpable->id}";

    $otpCode = 123456;
    $ttl = now()->addMinutes(5);

    Cache::put(
        key: $cacheKey,
        value: $otpCode,
        ttl: $ttl
    );

    Event::fakeFor(function () use ($otpable, $otpCode, $cacheKey) {

        $verified = (new ValidateOtp)->handle(
            otpable: $otpable,
            otp: $otpCode
        );

        expect($verified)->toBeTrue();

        Event::assertDispatched(OtpValidationSuccess::class);

        expect(Cache::get($cacheKey))->toBeNull();

    }, [OtpValidationSuccess::class]);
});

it('can not validate OTP with invalid code', function () {
    // Mock a model
    $otpable = new class extends Model
    {
        public function getQualifiedKeyName(): string
        {
            return 'users.id';
        }
    };
    $otpable->id = 1;

    $cacheKey = "otp.{$otpable->getQualifiedKeyName()}.{$otpable->id}";

    $otpCode = 123456;
    $ttl = now()->addMinutes(5);

    Cache::put(
        key: $cacheKey,
        value: $otpCode,
        ttl: $ttl
    );

    Event::fakeFor(function () use ($otpable, $otpCode, $cacheKey) {

        $verified = (new ValidateOtp)->handle(
            otpable: $otpable,
            otp: $otpCode + 2
        );

        expect($verified)->toBeFalse();

        Event::assertDispatched(OtpValidationFailed::class);

        expect(Cache::get($cacheKey))->toBe($otpCode);

    }, [OtpValidationFailed::class]);
});

it('can not validate OTP with expired code', function () {
    // Mock a model
    $otpable = new class extends Model
    {
        public function getQualifiedKeyName(): string
        {
            return 'users.id';
        }
    };
    $otpable->id = 1;

    $cacheKey = "otp.{$otpable->getQualifiedKeyName()}.{$otpable->id}";

    $otpCode = 123456;
    $ttl = now()->subMinutes(5);

    Cache::put(
        key: $cacheKey,
        value: $otpCode,
        ttl: $ttl
    );

    Event::fakeFor(function () use ($otpable, $otpCode, $cacheKey) {

        $verified = (new ValidateOtp)->handle(
            otpable: $otpable,
            otp: $otpCode + 2
        );

        expect($verified)->toBeFalse();

        Event::assertDispatched(OtpValidationFailed::class);

        expect(Cache::get($cacheKey))->toBeNull();

    }, [OtpValidationFailed::class]);
});

it('has valid otp in cache', function () {
    $otpable = new class extends Model
    {
        use \DigitalTunnel\Otakit\Traits\Otakit;

        public function getQualifiedKeyName(): string
        {
            return 'users.id';
        }
    };

    $otpable->id = 1;

    $cacheKey = "otp.{$otpable->getQualifiedKeyName()}.{$otpable->id}";

    Cache::put(
        key: $cacheKey,
        value: 123456,
        ttl: now()->addMinutes(5)
    );

    expect($otpable->hasOtp())->toBeTrue();
});
