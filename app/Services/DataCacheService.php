<?php

namespace App\Services;

use Closure;
use Illuminate\Support\Facades\Cache;

/**
 * Class DataCacheService
 *
 * Provides a reusable abstraction over Laravel's caching system.
 *
 * Responsibilities:
 * - Standardize cache usage across the application
 * - Handle TTL (time-to-live) configuration
 * - Provide optional support for tagged caching
 * - Centralize cache invalidation logic
 *
 * Notes:
 * - Only cache serializable data (arrays, scalars)
 * - Avoid caching objects like DTOs unless explicitly handled
 */
class DataCacheService
{
    /**
     * Default cache TTL (in minutes)
    */
     protected int $ttlMinutes = 10;

     /**
     * Generic cache wrapper using Laravel Cache::remember
     *
     * @template T
     *
     * @param string        $key Cache key
     * @param Closure(): T  $callback Callback to execute if cache miss
     * @param int|null      $ttl Time-to-live in minutes (optional)
     *
     * @return T
     */
    public function remember(string $key, Closure $callback, ?int $ttl = null): mixed
    {
        return Cache::remember(
            $key,
            now()->addMinutes($ttl ?? $this->ttlMinutes),
            $callback
        );
    }

    /**
     * Cache wrapper specifically for application data (reports, etc.)
     *
     * Currently delegates to generic remember(), but can be extended
     * to support tagging, namespacing, or different TTL strategies.
     *
     * @template T
     *
     * @param string        $key Cache key
     * @param Closure(): T  $callback
     * @param int|null      $ttl Time-to-live in minutes
     *
     * @return T
     */
    public function rememberData(string $key, Closure $callback, ?int $ttl = null): mixed
    {
        // Future extension point (e.g., add tags, prefixes, etc.)
        return $this->remember($key, $callback, $ttl);

    }

    /**
     * Clear all cached report/data entries
     *
     * Uses cache tags if supported (Redis, Memcached),
     * otherwise falls back to clearing entire cache.
     *
     * WARNING:
     * - Cache::flush() will remove ALL cache entries (not just reports)
     *
     * @return void
     */
    public function clearCachedData(): void
    {
        if (method_exists(Cache::getStore(), 'tags')) {
             // Tagged cache (preferred for selective invalidation)
            Cache::tags(['reports'])->flush();
        } else {
            // Fallback: clears entire cache (use cautiously)
            Cache::flush();
        }
    }
}
