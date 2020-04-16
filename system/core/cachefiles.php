<?php
class cmsCacheFiles {

    private $cache_path;

    public function __construct($config) {
        $this->cache_path = $config->cache_path.'data/';
    }

    public function set($key, $value, $ttl){

        $data = array(
            'ttl'   => $ttl,
            'time'  => time(),
            'value' => $value
        );

        list($path, $file) = $this->getPathAndFile($key);

        @mkdir($path, 0777, true);
        @chmod($path, 0777);
        @chmod(pathinfo($path, PATHINFO_DIRNAME), 0777);

        return file_put_contents($file, '<?php return '.var_export($data, true).';');

    }

    public function has($key){

        list($path, $file) = $this->getPathAndFile($key);

        return file_exists($file);

    }

    public function get($key){

        list($path, $file) = $this->getPathAndFile($key);

        $data = include $file;
        if (!$data) { return false; }

        if (time() > $data['time'] + $data['ttl']){
            $this->clean($key);
            return false;
        }

        return $data['value'];

    }

    public function clean($key=false){

        if ($key){

            $path = $this->cache_path . str_replace('.', '/', $key);

            if(is_file($path.'.dat')){
                @unlink($path.'.dat');
            }
            return files_remove_directory($path);

        } else {

            return files_clear_directory($this->cache_path);

        }

    }

    public function getPathAndFile($key){

        $path = $this->cache_path.str_replace('.', '/', $key);

        return array(dirname($path), $path.'.dat');

    }

    public function start(){ return true; }
    public function stop(){ return true; }

    public function getStats(){
        return array();
    }

}
