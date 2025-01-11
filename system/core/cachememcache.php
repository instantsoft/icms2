<?php

class cmsCacheMemcache {

    /**
     * @var Memcache
     */
    private $memcache;
    /**
     * @var string
     */
    private $site_namespace = '';
    /**
     * @var cmsConfigs
     */
    private $config;

    public function isDependencySatisfied() {
        return extension_loaded('memcache') && class_exists('Memcache');
    }

    public function __construct(cmsConfigs $config) {

        $this->config = $config;

        $this->site_namespace = 'instantcms.' . sprintf('%u', crc32($config->host));
    }

    public function set($key, $value, $ttl) {
        return $this->memcache->set($this->getKey($key), serialize($value), false, $ttl);
    }

    public function has($key) {
        return true;
    }

    public function get($key) {

        $value = $this->memcache->get($this->getKey($key));
        if (!$value) {
            return false;
        }

        return unserialize($value);
    }

    public function clean($ns = false) {

        if ($ns) {

            return $this->memcache->increment($this->getNamespaceKey($ns));

        } else {

            return $this->memcache->flush();
        }
    }

    public function start() {

        $this->memcache = new Memcache;

        if (!$this->memcache->connect($this->config->cache_host, $this->config->cache_port)) {

            throw new Exception('Memcache connect error');
        }

        return true;
    }

    public function stop() {

        $this->memcache->close();

        return true;
    }

    public function testConnection() {
        // Memcache сразу устанавливает соединение
        return 1;
    }

    public function getStats() {
        return [];
    }

    private function getKey($_key) {

        $last_dot_pos = strrpos($_key, '.');

        if ($last_dot_pos === false) {

            $key = $_key;
            $ns  = '';

        } else {

            $key = substr($_key, $last_dot_pos + 1);
            $ns  = substr($_key, 0, $last_dot_pos);
        }

        $ns_value = $this->getNamespaceValue($ns);

        return "{$this->site_namespace}.{$ns_value}.{$ns}.{$key}";
    }

    private function getNamespaceValue($ns) {

        $namespace_key = $this->getNamespaceKey($ns);

        $ns_value = $this->memcache->get($namespace_key);

        if ($ns_value === false) {

            $ns_value = time();

            $this->memcache->set($namespace_key, $ns_value, 86400);
        }

        return $ns_value;
    }

    private function getNamespaceKey($ns) {
        return $this->site_namespace . '.namespace:' . $ns;
    }

}
