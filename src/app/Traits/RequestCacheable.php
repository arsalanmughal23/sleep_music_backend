<?php

namespace App\Traits;

use App\Helper\CacheQueryBuilder as Builder;
use Illuminate\Http\Request;

trait RequestCacheable
{
    public $cacheTime   = 5;
    public $cachePrefix = "reqc";
    public $avoidCache  = false;

    public function cacheRequest($params, $callback)
    {
        if ($this->avoidCache) {
            return $callback();
        }
        $key   = $this->getPrefix() . hash('sha256', http_build_query($params));
        $cache = $this->getCache();
        return $cache->remember($key, $this->cacheTime, $callback);
    }

    /**
     * Get the cache driver.
     *
     * @return \Illuminate\Cache\CacheManager
     */
    protected function getCache()
    {
//        return app('cache')->driver($this->cacheDriver);
        return app('cache');
    }

    protected function getPrefix()
    {
        if (property_exists($this, "reqcSuffix")) {
            return $this->cachePrefix . ":" . $this->reqcSuffix . ":";
        }
        return $this->cachePrefix . ":";
    }

    protected function mergeDefaultParamsWithControllerParams($controllerParams)
    {
        // Merge and return Repository, Limit Offset and ControllerParams
        return array_merge(array_values(config('repository.criteria.params')), $controllerParams, ['limit', 'offset']);
    }

    public function flushCache()
    {
        $cache = $this->getCache();
        $store = $cache->getStore();
        if (method_exists($store, "getRedis")) {
            $storePrefix = $store->getPrefix();
            $keys        = $store->getRedis()->keys($storePrefix . $this->getPrefix() . "*");
            // TODO: Find a better approach.
            foreach ($keys as $key) {
                $cache->delete(str_replace($storePrefix, "", $key));
            }
        }

    }
}