<?php

namespace Farisc0de\PhpFileUploading\Tests\Unit\RateLimiting;

use PHPUnit\Framework\TestCase;
use Farisc0de\PhpFileUploading\RateLimiting\InMemoryRateLimiter;

class InMemoryRateLimiterTest extends TestCase
{
    public function testIsAllowedWithinLimit(): void
    {
        $limiter = new InMemoryRateLimiter(5, 60);

        $this->assertTrue($limiter->isAllowed('user1'));
    }

    public function testIsAllowedExceedsLimit(): void
    {
        $limiter = new InMemoryRateLimiter(3, 60);

        $limiter->hit('user1');
        $limiter->hit('user1');
        $limiter->hit('user1');

        $this->assertFalse($limiter->isAllowed('user1'));
    }

    public function testHitIncrementsCounter(): void
    {
        $limiter = new InMemoryRateLimiter(5, 60);

        $this->assertEquals(5, $limiter->getRemainingAttempts('user1'));

        $limiter->hit('user1');

        $this->assertEquals(4, $limiter->getRemainingAttempts('user1'));
    }

    public function testGetRemainingAttempts(): void
    {
        $limiter = new InMemoryRateLimiter(10, 60);

        $limiter->hit('user1');
        $limiter->hit('user1');
        $limiter->hit('user1');

        $this->assertEquals(7, $limiter->getRemainingAttempts('user1'));
    }

    public function testReset(): void
    {
        $limiter = new InMemoryRateLimiter(5, 60);

        $limiter->hit('user1');
        $limiter->hit('user1');
        $limiter->hit('user1');

        $limiter->reset('user1');

        $this->assertEquals(5, $limiter->getRemainingAttempts('user1'));
    }

    public function testDifferentIdentifiersAreSeparate(): void
    {
        $limiter = new InMemoryRateLimiter(2, 60);

        $limiter->hit('user1');
        $limiter->hit('user1');

        $this->assertFalse($limiter->isAllowed('user1'));
        $this->assertTrue($limiter->isAllowed('user2'));
    }

    public function testGetConfig(): void
    {
        $limiter = new InMemoryRateLimiter(100, 3600);

        $config = $limiter->getConfig();

        $this->assertEquals(100, $config['limit']);
        $this->assertEquals(3600, $config['window']);
    }

    public function testCheckReturnsResult(): void
    {
        $limiter = new InMemoryRateLimiter(5, 60);

        $result = $limiter->check('user1');

        $this->assertTrue($result->isAllowed());
        $this->assertEquals(5, $result->getLimit());
        $this->assertEquals(4, $result->getRemaining()); // One hit from check
        $this->assertEquals('user1', $result->getIdentifier());
    }

    public function testCheckWhenLimited(): void
    {
        $limiter = new InMemoryRateLimiter(2, 60);

        $limiter->hit('user1');
        $limiter->hit('user1');

        $result = $limiter->check('user1');

        $this->assertFalse($result->isAllowed());
        $this->assertTrue($result->isLimited());
    }

    public function testGetRetryAfter(): void
    {
        $limiter = new InMemoryRateLimiter(1, 60);

        $limiter->hit('user1');

        $retryAfter = $limiter->getRetryAfter('user1');

        $this->assertGreaterThan(0, $retryAfter);
        $this->assertLessThanOrEqual(60, $retryAfter);
    }

    public function testGetRetryAfterWithNoHits(): void
    {
        $limiter = new InMemoryRateLimiter(5, 60);

        $this->assertEquals(0, $limiter->getRetryAfter('user1'));
    }

    public function testRateLimitResultHeaders(): void
    {
        $limiter = new InMemoryRateLimiter(5, 60);

        $result = $limiter->check('user1');
        $headers = $result->getHeaders();

        $this->assertArrayHasKey('X-RateLimit-Limit', $headers);
        $this->assertArrayHasKey('X-RateLimit-Remaining', $headers);
        $this->assertEquals(5, $headers['X-RateLimit-Limit']);
    }
}
