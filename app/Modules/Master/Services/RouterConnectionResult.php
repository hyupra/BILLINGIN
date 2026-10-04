<?php

namespace App\Modules\Master\Services;

readonly class RouterConnectionResult
{
    public function __construct(
        public bool $reachable,
        public bool $blocked,
        public string $message,
        public ?int $latencyMs,
    ) {}
}
