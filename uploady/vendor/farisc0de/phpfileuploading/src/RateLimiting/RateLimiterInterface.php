<?php

namespace Farisc0de\PhpFileUploading\RateLimiting;

/**
 * Interface for rate limiting implementations
 *
 * @package PhpFileUploading
 */
interface RateLimiterInterface
{
    /**
     * Check if the identifier is allowed to perform an action
     *
     * @param string $identifier Unique identifier (e.g., IP address, user ID)
     * @return bool True if allowed, false if rate limited
     */
    public function isAllowed(string $identifier): bool;

    /**
     * Record a hit for the identifier
     *
     * @param string $identifier Unique identifier
     */
    public function hit(string $identifier): void;

    /**
     * Get the number of remaining attempts for the identifier
     *
     * @param string $identifier Unique identifier
     * @return int Number of remaining attempts
     */
    public function getRemainingAttempts(string $identifier): int;

    /**
     * Get the time until the rate limit resets
     *
     * @param string $identifier Unique identifier
     * @return int Seconds until reset
     */
    public function getRetryAfter(string $identifier): int;

    /**
     * Reset the rate limit for an identifier
     *
     * @param string $identifier Unique identifier
     */
    public function reset(string $identifier): void;

    /**
     * Get the rate limit configuration
     *
     * @return array Configuration array with 'limit' and 'window' keys
     */
    public function getConfig(): array;

    /**
     * Check rate limit and return a result object
     *
     * This method checks if the identifier is allowed, records a hit if allowed,
     * and returns a RateLimitResult with all relevant information.
     *
     * @param string $identifier Unique identifier (e.g., IP address, user ID)
     * @return RateLimitResult The rate limit check result
     */
    public function check(string $identifier): RateLimitResult;
}
