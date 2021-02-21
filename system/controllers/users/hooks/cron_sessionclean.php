<?php

class onUsersCronSessionclean extends cmsAction {

    public $disallow_event_db_register = true;

    public function run(){

        if($this->cms_config->session_save_handler !== 'files'){
            return true;
        }

        $maxlifetime = $this->cms_config->session_maxlifetime ? $this->cms_config->session_maxlifetime*60 : ini_get('session.gc_maxlifetime');
        $now_time = time();

        $files = glob($this->cms_config->session_save_path.'/ses*');

        if (!$files) { return true; }

        foreach($files as $file) {

            if(is_file($file) && ($now_time - filemtime($file)) > $maxlifetime) {

                unlink($file);

            }

        }

    }

}
