<?php
class cmsCacheFiles {

    public function set($key, $value, $ttl){

        $data = array(
            'ttl' => $ttl,
            'time' => time(),
            'value' => serialize($value)
        );

        list($path, $file) = $this->getPathAndFile($key);

        @mkdir($path, 0777, true);

        return file_put_contents($file, serialize($data));

    }

    public function has($key){

        list($path, $file) = $this->getPathAndFile($key);

        return file_exists($file);

    }

    public function get($key){

        list($path, $file) = $this->getPathAndFile($key);

        $data = file_get_contents($file);

        if (!$data) { return false; }

        $data = unserialize($data);

        if (time() > $data['time'] + $data['ttl']){
            $this->clean($key);
            return false;
        }

        return unserialize($data['value']);

    }

    public function clean($key=false){

        if ($key){

            $path = cmsConfig::get('cache_path') . str_replace('.', '/', $key);

            return files_remove_directory($path);

        } else {

            return files_clear_directory(cmsConfig::get('cache_path'));

        }

    }

    public function getPathAndFile($key){

        $path = cmsConfig::get('cache_path') . str_replace('.', '/', $key);
        $file = explode('/', $path);

        $path = dirname($path);
        $file = $path . '/' . $file[sizeof($file)-1] . '.dat';

        return array($path, $file);

    }

    public function start(){ return true; }
    public function stop(){ return true; }

}
