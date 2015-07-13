<?php

class actionAdminInstall extends cmsAction {

    private $upload_name = 'package';
    private $upload_exts = 'zip,icp';

    public function run($do=false){

        // если нужно, передаем управление другому экшену
        if ($do){
            $this->runAction('install_'.$do, array_slice($this->params, 1));
            return;
        }

        $package_name = $this->request->get('package_name', $this->uploadPackage());
        $is_no_extract = $this->request->get('is_no_extract', false);

        if (!$is_no_extract && !$package_name){ return $this->showUploadForm(); }

        return $this->showPackageInfo($package_name, $is_no_extract);

    }

    private function showPackageInfo($package_name, $is_no_extract=false){

        $config = cmsConfig::getInstance();

        if (!$is_no_extract) { $this->extractPackage($package_name); }

        $manifest = $this->parsePackageManifest();

        if (isset($manifest['depends'])){
            $manifest['depends_results'] = $this->checkManifestDepends($manifest['depends']);
        }

        return cmsTemplate::getInstance()->render('install_package_info', array(
            'manifest' => $manifest
        ));

    }

    private function showUploadForm(){

        $errors = $this->checkErrors();

        return cmsTemplate::getInstance()->render('install_upload', array(
            'errors' => $errors,
        ));

    }

    private function checkErrors(){

        $config = cmsConfig::getInstance();

        $errors = array();

        if (!cmsCore::isWritable( $config->upload_path . $this->installer_upload_path )){
            $errors[] = array(
                'text' => sprintf(LANG_CP_INSTALL_NOT_WRITABLE, $config->upload_root . $this->installer_upload_path),
                'hint' => LANG_CP_INSTALL_NOT_WRITABLE_HINT,
                'fix' => LANG_CP_INSTALL_NOT_WRITABLE_FIX,
                'workaround' => sprintf(LANG_CP_INSTALL_NOT_WRITABLE_WA, $config->upload_root . $this->installer_upload_path)
            );
        }

        if (!class_exists('ZipArchive')){
            $errors[] = array(
                'text' => LANG_CP_INSTALL_NOT_ZIP,
                'hint' => LANG_CP_INSTALL_NOT_ZIP_HINT,
                'fix' => LANG_CP_INSTALL_NOT_ZIP_FIX,
                'workaround' => sprintf(LANG_CP_INSTALL_NOT_ZIP_WA, $config->upload_root . $this->installer_upload_path),
            );
        }

        return $errors ? $errors : false;

    }

    private function checkManifestDepends($depends){

        $results = array();

        if (isset($depends['core'])){

            $need = (int)str_pad(str_replace('.', '', $depends['core']), 6, '0');
            $has = (int)str_pad(str_replace('.', '', cmsCore::getVersion()), 6, '0');

            $results['core'] = ($need <= $has) ? true : false;

        }

        return $results;

    }

    private function parsePackageManifest(){

        $config = cmsConfig::getInstance();

        $path = $config->upload_path . $this->installer_upload_path;

        $ini_file = $path . '/' . "manifest.{$config->language}.ini";
        $ini_file_default = $path . '/' . "manifest.ru.ini";

        if (!file_exists($ini_file)){ $ini_file = $ini_file_default; }
        if (!file_exists($ini_file)){ return false; }

        $manifest = parse_ini_file($ini_file, true);

        if (file_exists($config->upload_path . $this->installer_upload_path . '/' . 'package')){
            $manifest['contents'] = $this->getPackageContentsList();
        } else {
			$manifest['contents'] = false;
		}		

        if (isset($manifest['info']['image'])){
            $manifest['info']['image'] = $config->upload_host . '/' .
                                            $this->installer_upload_path . '/' .
                                            $manifest['info']['image'];
        }

        return $manifest;

    }

    private function getPackageContentsList(){

        $config = cmsConfig::getInstance();

        $path = $config->upload_path . $this->installer_upload_path . '/' . 'package';

        if (!is_dir($path)) { return false; }

        return files_tree_to_array($path);

    }

    private function extractPackage($package_name){

        $config = cmsConfig::getInstance();

        $zip_dir = $config->upload_path . $this->installer_upload_path;
        $zip_file =  $zip_dir . '/' . $package_name;

        $zip = new ZipArchive();

        if (!$zip->open( $zip_file )){
            cmsUser::addSessionMessage(LANG_CP_INSTALL_ZIP_ERROR, 'error');
            $this->redirectBack();
        }

        $zip->extractTo($zip_dir);
        $zip->close();

        unlink($zip_file);

        return true;

    }

    private function uploadPackage(){

        $config = cmsConfig::getInstance();

        $uploader = new cmsUploader();

        if (!$uploader->isUploaded( $this->upload_name )){ return false; }

        files_clear_directory($config->upload_path . $this->installer_upload_path);

        $result = $uploader->uploadForm($this->upload_name, $this->upload_exts, 0, $this->installer_upload_path);

        if (!$result['success']){
            cmsUser::addSessionMessage($result['error'], 'error');
            return false;
        }

        return $result['name'];

    }

}
