<?php

namespace App\Modules\Master\Services;

/**
 * Pure IP-range check, deliberately separate from RouterConnectionTester so
 * it's testable without opening real sockets. Private LAN ranges (10/8,
 * 172.16/12, 192.168/16) are intentionally NOT blocked: MikroTik routers
 * legitimately live there, reached over VPN, per
 * docs/spec/BACKEND-BILLINGIN-laravel-sqlserver.md §2.2. Blocking those
 * would break the real feature, not just close an attack surface.
 */
class HostGuard
{
    private const BLOCKED_CIDRS = [
        '127.0.0.0/8',
        '169.254.0.0/16',
        '0.0.0.0/32',
        '::1/128',
    ];

    public static function isBlocked(string $ip): bool
    {
        foreach (self::BLOCKED_CIDRS as $cidr) {
            if (self::ipInCidr($ip, $cidr)) {
                return true;
            }
        }

        return false;
    }

    private static function ipInCidr(string $ip, string $cidr): bool
    {
        [$subnet, $bits] = explode('/', $cidr);

        if (str_contains($ip, ':') !== str_contains($subnet, ':')) {
            return false; // IPv4 vs IPv6 mismatch, never a match
        }

        $ipBin = inet_pton($ip);
        $subnetBin = inet_pton($subnet);

        if ($ipBin === false || $subnetBin === false) {
            return false;
        }

        $bits = (int) $bits;
        $bytes = intdiv($bits, 8);
        $remainder = $bits % 8;

        if ($bytes > 0 && substr($ipBin, 0, $bytes) !== substr($subnetBin, 0, $bytes)) {
            return false;
        }

        if ($remainder === 0) {
            return true;
        }

        $mask = ~(0xFF >> $remainder) & 0xFF;

        return (ord($ipBin[$bytes]) & $mask) === (ord($subnetBin[$bytes]) & $mask);
    }
}
