<?php

use App\Modules\Master\Services\RouterConnectionTester;

test('reports reachable when the connector succeeds', function () {
    $fakeHandle = fopen('php://memory', 'r');
    $tester = new RouterConnectionTester(fn ($ip, $port, $timeout) => $fakeHandle);

    $result = $tester->test('203.0.113.10', 8728);

    expect($result->reachable)->toBeTrue();
    expect($result->blocked)->toBeFalse();
});

test('reports unreachable when the connector fails, without being blocked', function () {
    $tester = new RouterConnectionTester(fn ($ip, $port, $timeout) => false);

    $result = $tester->test('203.0.113.10', 8728);

    expect($result->reachable)->toBeFalse();
    expect($result->blocked)->toBeFalse();
});

test('blocks a loopback host before ever calling the connector', function () {
    $called = false;
    $tester = new RouterConnectionTester(function (...$args) use (&$called) {
        $called = true;

        return false;
    });

    $result = $tester->test('127.0.0.1', 8728);

    expect($result->blocked)->toBeTrue();
    expect($called)->toBeFalse();
});

test('blocks a hostname that resolves to a blocked range, not just a literal blocked IP', function () {
    // localtest.me is a stable public DNS entry that resolves to 127.0.0.1 —
    // this is exactly the resolve-then-check bypass HostGuard alone can't
    // catch; RouterConnectionTester must resolve before checking.
    $called = false;
    $tester = new RouterConnectionTester(function (...$args) use (&$called) {
        $called = true;

        return false;
    });

    $result = $tester->test('localtest.me', 8728);

    expect($result->blocked)->toBeTrue();
    expect($called)->toBeFalse();
});
