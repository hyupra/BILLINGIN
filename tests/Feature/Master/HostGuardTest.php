<?php

use App\Modules\Master\Services\HostGuard;

test('loopback and unspecified addresses are blocked', function () {
    expect(HostGuard::isBlocked('127.0.0.1'))->toBeTrue();
    expect(HostGuard::isBlocked('::1'))->toBeTrue();
    expect(HostGuard::isBlocked('0.0.0.0'))->toBeTrue();
});

test('link-local and cloud metadata addresses are blocked', function () {
    expect(HostGuard::isBlocked('169.254.169.254'))->toBeTrue();
});

test('private LAN ranges are allowed, matching how MikroTik routers are actually reached', function () {
    expect(HostGuard::isBlocked('10.0.0.5'))->toBeFalse();
    expect(HostGuard::isBlocked('172.16.0.5'))->toBeFalse();
    expect(HostGuard::isBlocked('192.168.1.1'))->toBeFalse();
});

test('public addresses are allowed', function () {
    expect(HostGuard::isBlocked('8.8.8.8'))->toBeFalse();
});
