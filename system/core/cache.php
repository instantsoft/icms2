<?php
class cmsCache {

    private static $instance;

    private $cacher;

    public $query_count = 0;


    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    public function __construct() {

        $cacher_class = 'cmsCache' . string_to_camel('_', cmsConfig::get('cache_method'));

        $this->cacher = new $cacher_class();

    }

    public function start() {
        if (!cmsConfig::get('cache_enabled')) { return false; }
        $this->cacher->start();
    }

    public function stop() {
        if (!cmsConfig::get('cache_enabled')) { return false; }
        $this->cacher->stop();
    }

    public function set($key, $value, $ttl=false){

        $config = cmsConfig::getInstance();

        if (!$config->cache_enabled) { return false; }

        if (!$ttl) { $ttl = $config->cache_ttl; }

        return $this->cacher->set($key, $value, $ttl);

    }

    public function has($key){

        if (!cmsConfig::get('cache_enabled')) { return false; }

        return $this->cacher->has($key);

    }

    public function get($key){

        if (!cmsConfig::get('cache_enabled')) { return false; }

        if (!$this->has($key)){ return false; }

        $value = $this->cacher->get($key);

        if (cmsConfig::get('debug') && $value) {
            $this->query_count++;
        }

        return $value;

    }

    public function clean($key){

        if (!cmsConfig::get('cache_enabled')) { return false; }

        return $this->cacher->clean($key);

    }

}
