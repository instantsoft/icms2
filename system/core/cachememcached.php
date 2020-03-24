<?php
class cmsCacheMemcached {

    private $memcached;
    private $site_namespace;
    private $config;

    public function __construct($config) {

        $this->config = $config;

        $this->site_namespace = 'instantcms.'.sprintf('%u', crc32($config->host));

    }

    public function set($key, $value, $ttl){

        return $this->memcached->set($this->getKey($key), serialize($value), $ttl);

    }

    public function has($key){
        return true;
    }

    public function get($key){

        $value = $this->memcached->get($this->getKey($key));
        if (!$value) { return false; }

        return unserialize($value);

    }

    public function clean($ns = false){

        if ($ns){

            return $this->memcached->increment($this->getNamespaceKey($ns));

        } else {

            return $this->memcached->flush();

        }

    }

    public function start(){

        $this->memcached = new Memcached();

        $this->memcached->addServer($this->config->cache_host, $this->config->cache_port) or die('Memcached connect error');

        return true;

    }

    public function stop(){
        $this->memcached->quit();
        return true;
    }

    public function getStats(){
        return $this->memcached->getStats();
    }

    private function getKey($_key){

        $key_path = explode('.', $_key);

        $key = array_pop($key_path);
        $ns  = implode('.', $key_path);

        return implode('.', array($this->site_namespace, $this->getNamespaceValue($ns), $ns, $key));

    }

    private function getNamespaceValue($ns){

        $ns_value = $this->memcached->get($this->getNamespaceKey($ns));

        if($ns_value === false) {

            $ns_value = time();

            $this->memcached->set($this->getNamespaceKey($ns), $ns_value, 86400);

        }

        return $ns_value;

    }

    private function getNamespaceKey($ns) {
        return $this->site_namespace.'.namespace:'.$ns;
    }

}
