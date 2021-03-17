<?php

class actionAdminCheckFtp extends cmsAction {

    public function run(){

        $host    = $this->request->get('host', '');
        $port    = $this->request->get('port', 0);
        $user    = $this->request->get('user', '');
        $pass    = $this->request->get('pass', '');
        $path    = $this->request->get('path', '');
        $is_pasv = $this->request->get('is_pasv', 0);

        $host = trim(str_replace('ftp://', '', $host), '/');
        if ($path != '/' ) {
            $path = '/' . trim($path, '/') . '/';
        }

        $errors = $files_list = array();
        $file_list = array('dirs' => array(), 'files' => array());

        $ftp_path = 'ftp://' . $host . $path;

        $connection = @ftp_connect($host, $port, 30);
        if (!$connection){ $errors['connect'] = LANG_CP_FTP_CONNECT_FAILED; }

        if(!$errors){

            $session = @ftp_login($connection, $user, $pass);
            if (!$session){ $errors['login'] = LANG_CP_FTP_AUTH_FAILED; }

            if(!$errors){

                if($is_pasv) { 
                    ftp_set_option($connection, FTP_USEPASVADDRESS, false);
                    ftp_pasv($connection, true); 
                }

                $_file_list = ftp_nlist($connection, $path);

                if (!$_file_list){
                    $errors['no_root'] = sprintf(LANG_CP_FTP_NO_ROOT, $ftp_path);
                }

                foreach ($_file_list as $list) {
                    if(ftp_is_dir($connection, $list)){
                        $file_list['dirs'][] = basename($list);
                    } else {
                        $file_list['files'][] = basename($list);
                    }
                }

                if(!$errors){

                    $check_dirs = array(
                        'system/core'   => 'core.php',
                        'system/config' => 'config.php'
                    );

                    foreach ($check_dirs as $dir => $file) {

                        $contents = ftp_nlist($connection, $path . $dir);

                        if (is_array($contents)){
                            foreach ($contents as $item) {
                                $files_list[] = basename($item);
                            }
                        }

                        if (!$files_list || !in_array($file, $files_list)) {

                            $errors['bad_root'] = sprintf(LANG_CP_FTP_BAD_ROOT, $ftp_path); break;

                        }

                    }

                }

            }

            ftp_close($connection);

        }

        return $this->cms_template->render('check_ftp', array(
            'ftp_path'  => $ftp_path,
            'errors'    => $errors,
            'file_list' => $file_list
        ));

    }

}

function ftp_is_dir($conn_id,  $dir) {
    if( @ftp_chdir($conn_id, $dir )) {
        ftp_chdir($conn_id, '/../');
        return true;
    } else {
        return false;
    }
}
