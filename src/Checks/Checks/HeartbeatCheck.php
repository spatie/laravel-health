<?php

namespace Spatie\Health\Checks\Checks;

interface HeartbeatCheck
{
    public function getCacheStoreName(): string;

    public function useCacheStore(string $cacheStoreName): self;

    public function getCacheKey(): string;

    public function cacheKey(string $cacheKey): self;

    public function heartbeatMaxAgeInMinutes(int $heartbeatMaxAgeInMinutes): self;
}
