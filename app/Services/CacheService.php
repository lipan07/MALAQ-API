<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

class CacheService
{
    /**
     * Cache duration in minutes
     */
    const DEFAULT_CACHE_DURATION = 60;
    const CATEGORIES_CACHE_DURATION = 1440; // 24 hours
    const USER_PROFILE_CACHE_DURATION = 30; // 30 minutes
    const POSTS_CACHE_DURATION = 15; // 15 minutes

    /**
     * Cache key prefixes
     */
    const CATEGORIES_KEY = 'categories';
    const USER_PROFILE_KEY = 'user_profile';
    const POSTS_KEY = 'posts';
    const USER_STATUS_KEY = 'user_status';

    /**
     * Get cached categories
     */
    public static function getCategories()
    {
        return Cache::remember(self::CATEGORIES_KEY, self::CATEGORIES_CACHE_DURATION, function () {
            return \App\Models\Category::select('id', 'name', 'parent_id', 'guard_name')
                ->orderBy('name')
                ->get();
        });
    }

    /**
     * Cache user profile data
     */
    public static function cacheUserProfile($userId, $profileData)
    {
        $key = self::USER_PROFILE_KEY . ':' . $userId;
        Cache::put($key, $profileData, self::USER_PROFILE_CACHE_DURATION);
    }

    /**
     * Get cached user profile
     */
    public static function getUserProfile($userId)
    {
        $key = self::USER_PROFILE_KEY . ':' . $userId;
        return Cache::get($key);
    }

    /**
     * Cache posts with filters
     */
    public static function cachePosts($filters, $posts)
    {
        $key = self::POSTS_KEY . ':' . md5(serialize($filters));
        Cache::put($key, $posts, self::POSTS_CACHE_DURATION);
    }

    /**
     * Get cached posts
     */
    public static function getCachedPosts($filters)
    {
        $key = self::POSTS_KEY . ':' . md5(serialize($filters));
        return Cache::get($key);
    }

    /**
     * Cache user online status
     */
    public static function cacheUserStatus($userId, $status)
    {
        $key = self::USER_STATUS_KEY . ':' . $userId;
        Cache::put($key, $status, 5); // 5 minutes cache for status
    }

    /**
     * Get cached user status
     */
    public static function getUserStatus($userId)
    {
        $key = self::USER_STATUS_KEY . ':' . $userId;
        return Cache::get($key);
    }

    /**
     * Clear cache by pattern
     */
    public static function clearCacheByPattern($pattern)
    {
        if (config('cache.default') === 'redis') {
            $keys = Redis::keys($pattern);
            if (!empty($keys)) {
                Redis::del($keys);
            }
        } else {
            // For file cache, we need to clear specific keys
            Cache::forget($pattern);
        }
    }

    /**
     * Clear user-related cache
     */
    public static function clearUserCache($userId)
    {
        self::clearCacheByPattern(self::USER_PROFILE_KEY . ':' . $userId);
        self::clearCacheByPattern(self::USER_STATUS_KEY . ':' . $userId);
    }

    /**
     * Clear posts cache
     */
    public static function clearPostsCache()
    {
        self::clearCacheByPattern(self::POSTS_KEY . ':*');
    }

    /**
     * Warm up cache with frequently accessed data
     */
    public static function warmUpCache()
    {
        // Pre-load categories
        self::getCategories();
        
        // Pre-load active users
        $activeUsers = \App\Models\User::where('status', 'online')
            ->select('id', 'name', 'status', 'last_activity')
            ->limit(100)
            ->get();
            
        foreach ($activeUsers as $user) {
            self::cacheUserStatus($user->id, $user->status);
        }
    }
}
