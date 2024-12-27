<?php

class CmsCacheRedis {

    /**
     * @var Redis
     */
    private $redis;

    /**
     * @var string
     */
    private $site_namespace = '';

    /**
     * @var object
     */
    private $config;

    public function __construct($config) {
        $this->config = $config;
        $this->site_namespace = 'instantcms.' . sprintf('%u', crc32($config->host));
    }

    public function set($key, $value, $ttl) {
        $value = is_array($value) || is_object($value) ? json_encode($value) : $value;
        return $this->redis->setex($this->getKey($key), $ttl, $value);
    }

    public function has($key) {
        return $this->redis->exists($this->getKey($key)) > 0;
    }

    public function get($key) {
        $value = $this->redis->get($this->getKey($key));
        $decoded = json_decode($value, true);
        return json_last_error() === JSON_ERROR_NONE ? $decoded : $value;
    }

    public function clean($ns = false) {
        if (!$ns) {
            return $this->redis->flushDB();
        }

        return $this->redis->incr($this->getNamespaceKey($ns));
    }

    public function start() {
        $this->redis = new Redis();
        if (!$this->redis->connect($this->config->cache_host, $this->config->cache_port)) {
            throw new Exception('Redis connect error');
        }
        return true;
    }

    public function stop() {
        $this->redis->close();
        return true;
    }

    public function getStats() {
        return $this->redis->info();
    }

    private function getKey($_key) {
        $key_path = explode('.', $_key);
        $key = array_pop($key_path);
        $ns  = implode('.', $key_path);
        return implode('.', [$this->site_namespace, $this->getNamespaceValue($ns), $ns, $key]);
    }

    private function getNamespaceValue($ns) {
        $ns_value = $this->redis->get($this->getNamespaceKey($ns));
        if ($ns_value === false) {
            $ns_value = time();
            $this->redis->setex($this->getNamespaceKey($ns), 86400, $ns_value);
        }
        return $ns_value;
    }

    private function getNamespaceKey($ns) {
        return $this->site_namespace . '.namespace:' . $ns;
    }
}
