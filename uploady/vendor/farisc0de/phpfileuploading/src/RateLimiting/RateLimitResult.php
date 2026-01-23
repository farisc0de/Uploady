<?php

namespace Farisc0de\PhpFileUploading\RateLimiting;

/**
 * Represents the result of a rate limit check
 *
 * @package PhpFileUploading
 */
class RateLimitResult
{
    private bool $allowed;
    private int $limit;
    private int $remaining;
    private int $retryAfter;
    private string $identifier;

    public function __construct(
        bool $allowed,
        int $limit,
        int $remaining,
        int $retryAfter,
        string $identifier
    ) {
        $this->allowed = $allowed;
        $this->limit = $limit;
        $this->remaining = $remaining;
        $this->retryAfter = $retryAfter;
        $this->identifier = $identifier;
    }

    /**
     * Check if the request is allowed
     */
    public function isAllowed(): bool
    {
        return $this->allowed;
    }

    /**
     * Check if the request is rate limited
     */
    public function isLimited(): bool
    {
        return !$this->allowed;
    }

    /**
     * Get the rate limit
     */
    public function getLimit(): int
    {
        return $this->limit;
    }

    /**
     * Get remaining attempts
     */
    public function getRemaining(): int
    {
        return $this->remaining;
    }

    /**
     * Get seconds until retry is allowed
     */
    public function getRetryAfter(): int
    {
        return $this->retryAfter;
    }

    /**
     * Get the identifier
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    /**
     * Get headers for HTTP response
     */
    public function getHeaders(): array
    {
        $headers = [
            'X-RateLimit-Limit' => $this->limit,
            'X-RateLimit-Remaining' => max(0, $this->remaining),
        ];

        if (!$this->allowed) {
            $headers['Retry-After'] = $this->retryAfter;
        }

        return $headers;
    }

    /**
     * Convert to array
     */
    public function toArray(): array
    {
        return [
            'allowed' => $this->allowed,
            'limit' => $this->limit,
            'remaining' => $this->remaining,
            'retry_after' => $this->retryAfter,
            'identifier' => $this->identifier,
        ];
    }
}
