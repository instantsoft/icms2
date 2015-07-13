<?php

class actionAdminInstallFinish extends cmsAction {

    public function run(){

        $config = cmsConfig::getInstance();

        $path = $config->upload_path . $this->installer_upload_path;
        $path_relative = $config->upload_root . $this->installer_upload_path;

        $installer_path = $path . '/' . 'install.php';
        $sql_dump_path = $path . '/' . 'install.sql';

		$this->importPackageDump($sql_dump_path);
        $this->runPackageInstaller($installer_path);        

        $is_cleared = files_clear_directory($path);

        return cmsTemplate::getInstance()->render('install_finish', array(
            'is_cleared' => $is_cleared,
            'path_relative' => $path_relative,
        ));

    }

    public function runPackageInstaller($file){

        if (!file_exists($file)) { return false; }

        @chmod($file, 0755);

        include_once $file;

        if (!function_exists('install_package')){ return false; }

        return call_user_func('install_package');

    }

    public function importPackageDump($file){

        if (!file_exists($file)) { return false; }

        $db = cmsDatabase::getInstance();

        return $db->importDump($file);

    }

}
