<?php

class actionAdminInstallFtp extends cmsAction {

    public function run(){

        if (!$this->getPackageContentsDir()){
            $this->redirectToAction('install/finish');
        }

        $form = $this->getForm('ftp');

        $ftp_account = cmsUser::getUPS('admin.install.ftp');

        $account = cmsUser::isSessionSet('ftp_account') ? cmsUser::sessionGet('ftp_account') : ($ftp_account ? $ftp_account : array());

        if ($this->request->has('submit')){

            $account = array_merge($account, $form->parse($this->request, true, $account));

            cmsUser::setUPS('admin.install.ftp', array(
                'host'    => $account['host'],
                'port'    => $account['port'],
                'path'    => $account['path'],
                'is_pasv' => $account['is_pasv']
            ));

            if($account['save_to_session']){
                cmsUser::sessionSet('ftp_account', $account);
            } else {
                cmsUser::sessionUnset('ftp_account');
            }

            $errors = $form->validate($this, $account);

            if ($errors){
                cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');
            }

            if (!$errors){
				$account['host'] = trim(str_replace('ftp://', '', $account['host']), '/');
				if ($account['path'] != '/' ) {
					$account['path'] = '/' . trim($account['path'], '/') . '/';
				}
                $this->uploadPackageToFTP($account);
            }

        }

        return $this->cms_template->render('install_ftp', array(
            'manifest' => $this->parsePackageManifest(),
            'account'  => $account,
            'form'     => $form,
            'errors'   => isset($errors) ? $errors : false
        ));

    }

    private function getPackageContentsDir(){

        $config = cmsConfig::getInstance();

        $path = $config->upload_path . $this->installer_upload_path . '/' . 'package';

        if (!is_dir($path)) { return false; }

        return $path;

    }

    private function uploadPackageToFTP($account){

        $connection = @ftp_connect($account['host'], $account['port'], 30);
        if (!$connection){ cmsUser::addSessionMessage(LANG_CP_FTP_CONNECT_FAILED, 'error'); return false; }

        $session = @ftp_login($connection, $account['user'], $account['pass']);
        if (!$session){ cmsUser::addSessionMessage(LANG_CP_FTP_AUTH_FAILED, 'error'); return false; }

        if($account['is_pasv']) { ftp_pasv($connection, true); }

		if (!$this->checkDestination($connection, $account)){
			return false;
		}

        $src_dir = $this->getPackageContentsDir();
        $dst_dir = '/' . trim($account['path'], '/');

        try{
            $this->uploadDirectoryToFTP($connection, $src_dir, $dst_dir);
        } catch (Exception $e)  {
            ftp_close($connection);
            cmsUser::addSessionMessage($e->getMessage(), 'error');
            return false;
        }

        ftp_close($connection);

        $this->redirectToAction('install/finish');

        return true;

    }

    private function checkDestination($connection, $account) {

        $ftp_path = 'ftp://' . $account['host'] . $account['path'];

        $check_dirs = array(
            'system/core' => 'core.php',
            'system/config' => 'config.php'
        );

        if (!ftp_nlist($connection, $account['path'])){
            cmsUser::addSessionMessage(sprintf(LANG_CP_FTP_NO_ROOT, $ftp_path), 'error');
            return false;
        }

        $files_list = array();

        foreach ($check_dirs as $dir => $file) {

            $contents = ftp_nlist($connection, $account['path'] . $dir);

            if (is_array($contents)){
                foreach ($contents as $item) {
                    $files_list[] = basename($item);
                }
            }

            if (!$files_list || !in_array($file, $files_list)) {
                cmsUser::addSessionMessage(sprintf(LANG_CP_FTP_BAD_ROOT, $ftp_path), 'error');
                return false;
            }

        }

        return true;

    }

    private function uploadDirectoryToFTP($conn_id, $src_dir, $dst_dir) {

        $d = dir($src_dir);
        $is_function_exists_ftp_chmod = function_exists('ftp_chmod');

        while($file = $d->read()) {

            if ($file != "." && $file != "..") {
                if (is_dir($src_dir."/".$file)) {

                    if (!@ftp_chdir($conn_id, $dst_dir."/".$file)) {
                        $result = @ftp_mkdir($conn_id, $dst_dir."/".$file);
                        if (!$result) {throw new Exception(LANG_CP_FTP_MKDIR_FAILED.': '.$dst_dir."/".$file);}
                        if($is_function_exists_ftp_chmod){
                            @ftp_chmod($conn_id, 0755, $dst_dir."/".$file);
                        }
                    }

                    $this->uploadDirectoryToFTP($conn_id, $src_dir."/".$file, $dst_dir."/".$file);

                } else {

                    $result = @ftp_put($conn_id, $dst_dir."/".$file, $src_dir."/".$file, FTP_BINARY);
                    if (!$result) { throw new Exception(LANG_CP_FTP_UPLOAD_FAILED); }
                    if($is_function_exists_ftp_chmod){
                        @ftp_chmod($conn_id, 0644, $dst_dir."/".$file);
                    }

                }
            }

        }

        $d->close();

    }


}
