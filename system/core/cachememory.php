<?php
class cmsCacheMemory {

    private $memcache;

    public function set($key, $value, $ttl){

        list($ns, $key) = $this->getNamespaceAndKey($key);

        $ns_value = $this->getNamespaceValue($ns);

        $key = implode('.', array($ns_value, $ns, $key));

        return $this->memcache->set($key, serialize($value), false, $ttl);

    }

    public function has($key){
        return true;
    }

    public function get($key){

        list($ns, $key) = $this->getNamespaceAndKey($key);

        $ns_value = $this->getNamespaceValue($ns);

        $key = implode('.', array($ns_value, $ns, $key));

        $value = $this->memcache->get($key);

        if (!$value) { return false; }

        return unserialize($value);

    }

    public function clean($ns=false){

        if ($ns){

            return $this->memcache->increment("namespace:{$ns}");

        } else {

            return $this->memcache->flush();

        }

    }

    public function start(){
        $config = cmsConfig::getInstance();
        $this->memcache = new Memcache;
        $this->memcache->connect($config->cache_host, $config->cache_port) or die('Memcache connect error');
        return true;
    }


    public function stop(){
        $this->memcache->close();
        return true;
    }

    private function getNamespaceAndKey($key){

        $ns = str_replace('.', '/', $key);
        $key = explode('/', $ns);

        $ns = dirname($ns);
        $key = $key[sizeof($key)-1];

        $ns = str_replace('/', '.', $ns);

        return array($ns, $key);

    }

    private function getNamespaceValue($ns){

        $ns_value = $this->memcache->get("namespace:{$ns}");

        if($ns_value===false) {
            $ns_value = 1;
            $this->memcache->set("namespace:{$ns}", $ns_value, false, 86400);
        }

        return $ns_value;

    }

}
