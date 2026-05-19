<?php

class cmsCacheFiles {

    private $cache_path;

    public function isDependencySatisfied() {
        return is_writable($this->cache_path);
    }

    public function __construct(cmsConfigs $config) {
        $this->cache_path = $config->cache_path . 'data/';
    }

    public function set(string $key, $value, $ttl) {

        [$path, $file] = $this->getPathAndFile($key);

        if (!is_dir($path) && !mkdir($path, 0777, true) && !is_dir($path)) {
            return false;
        }

        $data = [
            'e' => time() + (int) $ttl,
            'v' => $value
        ];

        $json = json_encode(
            $data,
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        );

        if ($json === false) {
            return false;
        }

        $tmp = $file . '.tmp';

        $result = file_put_contents($tmp, $json, LOCK_EX);

        if ($result === false) {
            @unlink($tmp);
            return false;
        }

        if (!rename($tmp, $file)) {
            @unlink($tmp);
            return false;
        }

        return true;
    }

    public function has(string $key) {

        [$path, $file] = $this->getPathAndFile($key);

        return is_file($file);
    }

    public function get(string $key) {

        [$path, $file] = $this->getPathAndFile($key);

        if (!is_file($file)) {
            return false;
        }

        $json = file_get_contents($file);

        if ($json === false) {
            return false;
        }

        $data = json_decode($json, true);

        if (!is_array($data) || !isset($data['e']) || time() >= $data['e']) {
            $this->clean($key);
            return false;
        }

        return array_key_exists('v', $data) ? $data['v'] : false;
    }

    public function clean($key = false) {

        if ($key) {

            [$path, $file] = $this->getPathAndFile($key);

            if (is_file($file)) {
                @unlink($file);
            }

            if (is_dir($path)) {
                @rmdir($path);
            }

            return true;
        }

        return files_clear_directory($this->cache_path);
    }

    public function getPathAndFile($key) {

        $path = $this->cache_path . str_replace('.', '/', $key);

        return [
            dirname($path),
            $path . '.json'
        ];
    }

    public function start() {
        return true;
    }

    public function stop() {
        return true;
    }

    public function testConnection() {
        return 1;
    }

    public function getStats() {
        return [];
    }

}
