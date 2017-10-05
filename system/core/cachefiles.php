<?php
class cmsCacheFiles {

    private $cache_path;

    public function __construct($config) {
        $this->cache_path = $config->cache_path.'data/';
    }

    public function set($key, $value, $ttl){

        list($path, $file) = $this->getPathAndFile($key);

        @mkdir($path, 0777, true);
        @chmod($path, 0777);
        @chmod(pathinfo($path, PATHINFO_DIRNAME), 0777);

        return $this->_write($file, $value, time() + $ttl);

    }

    public function has($key){

        list($path, $file) = $this->getPathAndFile($key);

        return file_exists($file);

    }

    public function get($key){

        list(, $file) = $this->getPathAndFile($key);

        return $this->_read($file, $key);

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
    
    
    
    private function _read($file, $key){

        if (!file_exists($file))
        {
            return false;
        }

        if (!($handle = @fopen($file, 'rb')))
        {
            return false;
        }

        fgets($handle);

        $data = false;
        $line = 0;

        while (($buffer = fgets($handle)) && !feof($handle))
        {
            $buffer = substr($buffer, 0, -1);

            if (!is_numeric($buffer))
            {
                break;
            }

            if ($line == 0)
            {
                $expires = (int) $buffer;

                if (time() >= $expires)
                {
                    break;
                }
            }
            else if ($line == 1)
            {
                $bytes = (int) $buffer;

                if (!$bytes)
                {
                    break;
                }

                $data = fread($handle, $bytes);
                
                fread($handle, 1);

                if (!feof($handle))
                {
                    
                    $data = false;
                }
                break;
            }
            else
            {
                
                break;
            }
            $line++;
        }
        fclose($handle);

        
        $data = ($data !== false) ? @unserialize($data) : $data;

        if ($data === false)
        {
            $this->clean($key);
            return false;
        }

        return $data;
    }

    private function _write ($file, $data = null, $expires = 0){

        if (($handle = @fopen($file, 'wb')) === false)
        {
            return false;
        }
        
        fwrite($handle, '<' . '?php exit; ?' . '>');

        fwrite($handle, "\n" . $expires . "\n");

        $data = serialize($data);

        fwrite($handle, strlen($data) . "\n");
        fwrite($handle, $data);

        fclose($handle);

        if (function_exists('opcache_invalidate'))
        {
            @opcache_invalidate($file);
        }

        return true;
    }

}
