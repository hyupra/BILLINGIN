<?php

namespace App\Modules\Master\Services;

class RouterConnectionTester
{
    public function __construct(private readonly ?\Closure $connector = null) {}

    public function test(string $host, int $port): RouterConnectionResult
    {
        $ip = $this->resolve($host);

        if ($ip === null) {
            return new RouterConnectionResult(false, true, 'Host tidak bisa di-resolve.', null);
        }

        if (HostGuard::isBlocked($ip)) {
            return new RouterConnectionResult(false, true, 'Host ini tidak diizinkan untuk diuji.', null);
        }

        $connect = $this->connector ?? function (string $ip, int $port, int $timeout) {
            $errno = 0;
            $errstr = '';

            return @fsockopen($ip, $port, $errno, $errstr, $timeout);
        };

        $start = microtime(true);
        $connection = $connect($ip, $port, 3);
        $latencyMs = (int) round((microtime(true) - $start) * 1000);

        if ($connection === false || $connection === null) {
            return new RouterConnectionResult(false, false, 'Tidak bisa terhubung.', $latencyMs);
        }

        if (is_resource($connection)) {
            fclose($connection);
        }

        return new RouterConnectionResult(true, false, 'Terhubung.', $latencyMs);
    }

    /**
     * IPv4-only resolution (gethostbyname): routers are given LAN IPv4
     * addresses in practice, and fsockopen below is tried against whatever
     * this returns — adding AAAA/IPv6 resolution is unnecessary complexity
     * for S1's actual use case.
     */
    private function resolve(string $host): ?string
    {
        if (filter_var($host, FILTER_VALIDATE_IP)) {
            return $host;
        }

        $ip = gethostbyname($host);

        return $ip === $host ? null : $ip;
    }
}
