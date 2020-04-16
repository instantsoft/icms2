<?php
class cmsCacheMemory {

    private $memcache;
    private $site_namespace;
    private $config;

    public function __construct($config) {

        $this->config = $config;

        $this->site_namespace = 'instantcms.'.sprintf('%u', crc32($config->host));

    }

    public function set($key, $value, $ttl){

        return $this->memcache->set($this->getKey($key), serialize($value), false, $ttl);

    }

    public function has($key){
        return true;
    }

    public function get($key){

        $value = $this->memcache->get($this->getKey($key));
        if (!$value) { return false; }

        return unserialize($value);

    }

    public function clean($ns = false){

        if ($ns){

            return $this->memcache->increment($this->getNamespaceKey($ns));

        } else {

            return $this->memcache->flush();

        }

    }

    public function start(){

        $this->memcache = new Memcache;

        $this->memcache->connect($this->config->cache_host, $this->config->cache_port) or die('Memcache connect error');

        return true;

    }


    public function stop(){
        $this->memcache->close();
        return true;
    }

    public function getStats(){
        return array();
    }

    private function getKey($_key){

        $key_path = explode('.', $_key);

        $key = array_pop($key_path);
        $ns  = implode('.', $key_path);

        return implode('.', array($this->site_namespace, $this->getNamespaceValue($ns), $ns, $key));

    }

    private function getNamespaceValue($ns){

        $ns_value = $this->memcache->get($this->getNamespaceKey($ns));

        if($ns_value === false) {

            $ns_value = time();

            $this->memcache->set($this->getNamespaceKey($ns), $ns_value, false, 86400);

        }

        return $ns_value;

    }

    private function getNamespaceKey($ns) {
        return $this->site_namespace.'.namespace:'.$ns;
    }

}
