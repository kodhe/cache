<?php

declare(strict_types=1);

namespace Kodhe\Framework\Cache\Tests;

use PHPUnit\Framework\TestCase;
use Kodhe\Framework\Cache\Cache;

/**
 * Test Cache library compatibility with CodeIgniter 3 API
 *
 * Uses the Dummy driver so tests run without external services.
 */
class CacheCompatTest extends TestCase
{
    private Cache $cache;

    protected function setUp(): void
    {
        parent::setUp();
        $this->cache = new Cache(['adapter' => 'dummy', 'backup' => 'dummy']);
    }

    public function testInstantiation(): void
    {
        $this->assertInstanceOf(Cache::class, $this->cache);
    }

    public function testDefaultKeyPrefix(): void
    {
        $this->assertSame('', $this->cache->key_prefix);
    }

    public function testKeyPrefixFromConfig(): void
    {
        $cache = new Cache(['adapter' => 'dummy', 'key_prefix' => 'pre_']);
        $this->assertSame('pre_', $cache->key_prefix);
    }

    public function testIsSupportedDummy(): void
    {
        $this->assertTrue($this->cache->is_supported('dummy'));
    }

    public function testIsSupportedUnknownDriver(): void
    {
        $this->assertFalse($this->cache->is_supported('nonexistent_driver_xyz'));
    }

    public function testSaveAndGetReturnsFalseForDummy(): void
    {
        // Dummy driver never stores anything
        $this->assertFalse($this->cache->save('test_key', 'value', 60));
        $this->assertFalse($this->cache->get('test_key'));
    }

    public function testDeleteReturnsFalseForDummy(): void
    {
        $this->assertFalse($this->cache->delete('test_key'));
    }

    public function testIncrementDecrementReturnFalseForDummy(): void
    {
        $this->assertFalse($this->cache->increment('test_key'));
        $this->assertFalse($this->cache->decrement('test_key'));
    }

    public function testCleanReturnsFalseForDummy(): void
    {
        $this->assertFalse($this->cache->clean());
    }

    public function testGetMetadataReturnsFalseForDummy(): void
    {
        $this->assertFalse($this->cache->get_metadata('test_key'));
    }

    public function testCacheInfoReturnsArray(): void
    {
        $info = $this->cache->cache_info('user');
        $this->assertIsArray($info);
    }

    public function testUnsupportedAdapterFallsBackToDummy(): void
    {
        // Unknown adapter + unknown backup must silently fall back to dummy
        $cache = new Cache(['adapter' => 'redis_not_available_xyz', 'backup' => 'redis_not_available_xyz']);
        $this->assertFalse($cache->get('anything'));
    }
}
