<?php

class cmsCache {

    private static $instance;
    private $cacher;
    private $cacher_name;
    private $cache_ttl;

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    public static function getCacher($config) {

        $cacher_class = 'cmsCache' . string_to_camel('_', $config->cache_method);

        return new $cacher_class($config);
    }

    public function __construct() {

        $config = cmsConfig::getInstance();

        if ($config->cache_enabled) {

            $this->cacher = self::getCacher($config);
            $this->cacher_name = $config->cache_method;

            $this->cache_ttl = $config->cache_ttl;
        }
    }

    public function __call($method_name, $arguments) {

        // кеширование отключено
        if (!isset($this->cacher)) {
            return false;
        }

        // есть метод здесь, вызываем его
        if (method_exists($this, '_' . $method_name)) {
            return call_user_func_array([$this, '_' . $method_name], $arguments);
        }
        // есть метод в кешере, вызываем его
        if (method_exists($this->cacher, $method_name)) {
            return call_user_func_array([$this->cacher, $method_name], $arguments);
        }
        // ничего нет
        trigger_error('not defined method name ' . $method_name, E_USER_NOTICE);

        return false;
    }

    private function _set($key, $value, $ttl = false) {

        if (!$ttl) { $ttl = $this->cache_ttl; }

        cmsDebugging::pointStart('cache');

        $result = $this->cacher->set($key, $value, $ttl);

        cmsDebugging::pointProcess('cache', [
            'data' => $this->cacher_name.' => set => '.$key,
            'context' => [
                'target' => $this->cacher_name,
                'subject' => $key
            ]
        ], 5);

        return $result;
    }

    private function _get($key) {

        if (!$this->cacher->has($key)) {
            return false;
        }

        cmsDebugging::pointStart('cache');

        $value = $this->cacher->get($key);

        cmsDebugging::pointProcess('cache', [
            'data' => $this->cacher_name.' => get => '.$key,
            'context' => [
                'target' => $this->cacher_name,
                'subject' => $key
            ]
        ], 5);

        return $value;
    }

}
