<?php

namespace Farisc0de\PhpFileUploading\RateLimiting;

use RuntimeException;

/**
 * File-based rate limiter for persistent rate limiting across requests
 *
 * @package PhpFileUploading
 */
class FileRateLimiter implements RateLimiterInterface
{
    private int $limit;
    private int $window;
    private string $storagePath;

    /**
     * @param string $storagePath Directory to store rate limit data
     * @param int $limit Maximum number of requests allowed
     * @param int $window Time window in seconds
     */
    public function __construct(string $storagePath, int $limit = 60, int $window = 60)
    {
        $this->storagePath = rtrim($storagePath, DIRECTORY_SEPARATOR);
        $this->limit = $limit;
        $this->window = $window;

        $this->ensureStorageExists();
    }

    public function isAllowed(string $identifier): bool
    {
        $data = $this->getData($identifier);
        $this->cleanExpired($data);

        return count($data['hits']) < $this->limit;
    }

    public function hit(string $identifier): void
    {
        $data = $this->getData($identifier);
        $this->cleanExpired($data);

        $data['hits'][] = time();
        $this->saveData($identifier, $data);
    }

    public function getRemainingAttempts(string $identifier): int
    {
        $data = $this->getData($identifier);
        $this->cleanExpired($data);

        return max(0, $this->limit - count($data['hits']));
    }

    public function getRetryAfter(string $identifier): int
    {
        $data = $this->getData($identifier);

        if (empty($data['hits'])) {
            return 0;
        }

        $oldestHit = min($data['hits']);
        $expiresAt = $oldestHit + $this->window;

        return max(0, $expiresAt - time());
    }

    public function reset(string $identifier): void
    {
        $file = $this->getFilePath($identifier);
        if (file_exists($file)) {
            unlink($file);
        }
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
     * Get data for an identifier
     */
    private function getData(string $identifier): array
    {
        $file = $this->getFilePath($identifier);

        if (!file_exists($file)) {
            return ['hits' => []];
        }

        $content = file_get_contents($file);
        if ($content === false) {
            return ['hits' => []];
        }

        $data = json_decode($content, true);
        if (!is_array($data) || !isset($data['hits'])) {
            return ['hits' => []];
        }

        return $data;
    }

    /**
     * Save data for an identifier
     */
    private function saveData(string $identifier, array $data): void
    {
        $file = $this->getFilePath($identifier);
        $content = json_encode($data);

        if (file_put_contents($file, $content, LOCK_EX) === false) {
            throw new RuntimeException("Failed to save rate limit data: {$file}");
        }
    }

    /**
     * Get file path for an identifier
     */
    private function getFilePath(string $identifier): string
    {
        // Hash the identifier to create a safe filename
        $hash = hash('sha256', $identifier);
        return $this->storagePath . DIRECTORY_SEPARATOR . $hash . '.json';
    }

    /**
     * Remove expired hits from data
     */
    private function cleanExpired(array &$data): void
    {
        $cutoff = time() - $this->window;
        $data['hits'] = array_values(array_filter(
            $data['hits'],
            fn($timestamp) => $timestamp > $cutoff
        ));
    }

    /**
     * Ensure storage directory exists
     */
    private function ensureStorageExists(): void
    {
        if (!is_dir($this->storagePath)) {
            if (!mkdir($this->storagePath, 0755, true)) {
                throw new RuntimeException("Failed to create rate limit storage directory: {$this->storagePath}");
            }
        }

        if (!is_writable($this->storagePath)) {
            throw new RuntimeException("Rate limit storage directory is not writable: {$this->storagePath}");
        }
    }

    /**
     * Clean up old rate limit files
     */
    public function cleanup(): int
    {
        $deleted = 0;
        $cutoff = time() - ($this->window * 2); // Keep files for 2x the window

        foreach (glob($this->storagePath . '/*.json') as $file) {
            $mtime = filemtime($file);
            if ($mtime !== false && $mtime < $cutoff) {
                if (unlink($file)) {
                    $deleted++;
                }
            }
        }

        return $deleted;
    }
}
