<?php

namespace Farisc0de\PhpFileUploading\RateLimiting;

/**
 * In-memory rate limiter using sliding window algorithm
 * 
 * Note: This implementation stores data in memory and will reset on each request.
 * For production use, consider using FileRateLimiter or a Redis-based implementation.
 *
 * @package PhpFileUploading
 */
class InMemoryRateLimiter implements RateLimiterInterface
{
    private int $limit;
    private int $window;
    private array $hits = [];

    /**
     * @param int $limit Maximum number of requests allowed
     * @param int $window Time window in seconds
     */
    public function __construct(int $limit = 60, int $window = 60)
    {
        $this->limit = $limit;
        $this->window = $window;
    }

    public function isAllowed(string $identifier): bool
    {
        $this->cleanExpired($identifier);
        return count($this->hits[$identifier] ?? []) < $this->limit;
    }

    public function hit(string $identifier): void
    {
        $this->cleanExpired($identifier);

        if (!isset($this->hits[$identifier])) {
            $this->hits[$identifier] = [];
        }

        $this->hits[$identifier][] = time();
    }

    public function getRemainingAttempts(string $identifier): int
    {
        $this->cleanExpired($identifier);
        $currentHits = count($this->hits[$identifier] ?? []);
        return max(0, $this->limit - $currentHits);
    }

    public function getRetryAfter(string $identifier): int
    {
        if (!isset($this->hits[$identifier]) || empty($this->hits[$identifier])) {
            return 0;
        }

        $oldestHit = min($this->hits[$identifier]);
        $expiresAt = $oldestHit + $this->window;

        return max(0, $expiresAt - time());
    }

    public function reset(string $identifier): void
    {
        unset($this->hits[$identifier]);
    }

    public function getConfig(): array
    {
        return [
            'limit' => $this->limit,
            'window' => $this->window,
        ];
    }

    /**
     * Check rate limit and return result object
     */
    public function check(string $identifier): RateLimitResult
    {
        $allowed = $this->isAllowed($identifier);

        if ($allowed) {
            $this->hit($identifier);
        }

        return new RateLimitResult(
            $allowed,
            $this->limit,
            $this->getRemainingAttempts($identifier),
            $this->getRetryAfter($identifier),
            $identifier
        );
    }

    /**
     * Remove expired hits
     */
    private function cleanExpired(string $identifier): void
    {
        if (!isset($this->hits[$identifier])) {
            return;
        }

        $cutoff = time() - $this->window;
        $this->hits[$identifier] = array_filter(
            $this->hits[$identifier],
            fn($timestamp) => $timestamp > $cutoff
        );
    }
}
