<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Cache;

class CustomCache
{
    /**
     * This function is used to save data in cache with tag
     * @param $tag {string}
     * @param $key {string}
     * @param $data {mixed}
     * @param $expiresAt {numeric}
     */
    public function store($tag, $key, $data, $expiresAt = 1)
    {
        $cache_data = [];
        if( Cache::has($tag) ){
            $cache_data = Cache::get($tag);
            $cache_data[$key] = $data;
            Cache::forget($tag);
        } else {
            $cache_data[$key] = $data;
        }
        Cache::put($tag, $cache_data, now()->addDays($expiresAt));
        return;
    }

    public function get($tag, $key)
    {
        if( Cache::has($tag) ){
            $record = Cache::get($tag);
            if( !empty($record[$key]) )
                return $record[$key];
        }
        return [];
    }

    public function flush($tag, $key)
    {
        $record = Cache::get($tag);
        if( !empty($record[$key]) ){
            unset($record[$key]);
            Cache::forget($tag);
            Cache::put($tag, $record, now()->addDays(1));
            return true;
        } else {
            return false;
        }
    }

    public function flashAll($tag)
    {
        if( Cache::has($tag) ){
            Cache::forget($tag);
            return true;
        }
        return false;
    }
}
