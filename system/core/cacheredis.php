<?php

class cmsCacheRedis {

    /**
     * @var Redis
     */
    private $redis;

    /**
     * @var string
     */
    private $site_namespace = '';

    /**
     * @var cmsConfigs
     */
    private $config;

    public function isDependencySatisfied() {
        return extension_loaded('redis') && class_exists('Redis');
    }

    public function __construct(cmsConfigs $config) {

        $this->config         = $config;
        $this->site_namespace = 'instantcms.' . sprintf('%u', crc32($config->host));
    }

    public function set($key, $value, $ttl) {
        return $this->redis->setex($this->getKey($key), $ttl, $this->serialize($value));
    }

    public function has($key) {
        return $this->redis->exists($this->getKey($key)) > 0;
    }

    public function get($key) {

        $value = $this->redis->get($this->getKey($key));

        return !is_null($value) ? $this->unserialize($value) : false;
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

    public function testConnection() {
        return $this->redis->echo('hello') === 'hello' ? 1 : 0;
    }

    public function getStats() {
        return $this->redis->info();
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

        $ns_value = $this->redis->get($namespace_key);

        if ($ns_value === false) {

            $ns_value = time();

            $this->redis->setex($namespace_key, 86400, $ns_value);
        }

        return $ns_value;
    }

    private function getNamespaceKey($ns) {
        return $this->site_namespace . '.namespace:' . $ns;
    }

    private function serialize($value) {
        return is_numeric($value) && !in_array($value, [INF, -INF]) && !is_nan($value) ? $value : serialize($value);
    }

    private function unserialize($value) {
        return is_numeric($value) ? $value : unserialize($value);
    }

}
