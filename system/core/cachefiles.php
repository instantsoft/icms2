<?php

class cmsCacheFiles {

    private $cache_path;

    public function __construct($config) {
        $this->cache_path = $config->cache_path . 'data/';
    }

    public function set($key, $value, $ttl) {

        $data = [
            'ttl'   => $ttl,
            'time'  => time(),
            'value' => serialize($value)
        ];

        list($path, $file_path) = $this->getPathAndFile($key);

        @mkdir($path, 0777, true);
        @chmod($path, 0777);
        @chmod(pathinfo($path, PATHINFO_DIRNAME), 0777);

        $file_path_tmp = $file_path . '.tmp';

        $success = file_put_contents($file_path_tmp, '<?php return ' . var_export($data, true) . ';');

        if ($success) {
            rename($file_path_tmp, $file_path);
        }

        return $success;
    }

    public function has($key) {

        list($path, $file) = $this->getPathAndFile($key);

        return is_readable($file);
    }

    public function get($key) {

        list($path, $file) = $this->getPathAndFile($key);

        $data = include $file;
        if (!$data) {
            return false;
        }

        if (!isset($data['value']) ||
                !isset($data['time']) ||
                !isset($data['ttl']) ||
                time() > ($data['time'] + $data['ttl'])) {

            $this->clean($key);
            return false;
        }

        return unserialize($data['value']);
    }

    public function clean($key = false) {

        if ($key) {

            $path = $this->cache_path . str_replace('.', '/', $key);

            if (is_file($path . '.dat')) {
                @unlink($path . '.dat');
            }

            return files_remove_directory($path);
        } else {
            return files_clear_directory($this->cache_path);
        }

    }

    public function getPathAndFile($key) {

        $path = $this->cache_path . str_replace('.', '/', $key);

        return [dirname($path), $path . '.dat'];
    }

    public function start() {
        return true;
    }

    public function stop() {
        return true;
    }

    public function getStats() {
        return [];
    }

}
