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

        if (!$is_no_extract) { $this->extractPackage($package_name); }

        $manifest = $this->parsePackageManifest();

        $manifest['depends_results'] = $this->checkManifestDepends($manifest);

        // если пакет уже установлен, а мы пытаемся его еще раз установить, показываем сообщение
        if(!empty($manifest['package']['installed_version']) && $manifest['package']['action'] == 'install'){

            files_clear_directory(cmsConfig::get('upload_path') . $this->installer_upload_path);

            cmsUser::addSessionMessage(sprintf(LANG_CP_PACKAGE_DUBLE_INSTALL, $manifest['package']['installed_version']), 'error');

            $this->redirectToAction('install');

        }

        // если это пакет обновления, а полная версия не установлена
        if(isset($manifest['package']) && empty($manifest['package']['installed_version']) && $manifest['package']['action'] == 'update'){

            files_clear_directory(cmsConfig::get('upload_path') . $this->installer_upload_path);

            cmsUser::addSessionMessage(LANG_CP_PACKAGE_UPDATE_NOINSTALL, 'error');

            $this->redirectToAction('install');

        }

        // если это пакет обновления и обновляемая версия ниже существующей или равна
        if(!empty($manifest['package']['installed_version']) && $manifest['package']['action'] == 'update'){

            $package_v = $manifest['version']['major'].'.'.$manifest['version']['minor'].'.'.$manifest['version']['build'];

            $upd = (int)str_pad(str_replace('.', '', $package_v), 6, '0');
            $inst = (int)str_pad(str_replace('.', '', $manifest['package']['installed_version']), 6, '0');

            if($upd < $inst){

                files_clear_directory(cmsConfig::get('upload_path') . $this->installer_upload_path);

                cmsUser::addSessionMessage(sprintf(LANG_CP_PACKAGE_UPDATE_ERROR, $manifest['package']['type_hint'], $manifest['info']['title'], $package_v, $manifest['package']['installed_version']), 'error');

                $this->redirectToAction('install');

            }

            if($upd == $inst){

                files_clear_directory(cmsConfig::get('upload_path') . $this->installer_upload_path);

                cmsUser::addSessionMessage(LANG_CP_PACKAGE_UPDATE_IS_UPDATED, 'error');

                $this->redirectToAction('install');

            }

        }

        return $this->cms_template->render('install_package_info', array(
            'manifest' => $manifest
        ));

    }

    private function showUploadForm(){

        $errors = $this->checkErrors();

        return $this->cms_template->render('install_upload', array(
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
                'fix'  => LANG_CP_INSTALL_NOT_WRITABLE_FIX,
                'workaround' => sprintf(LANG_CP_INSTALL_NOT_WRITABLE_WA, $config->upload_root . $this->installer_upload_path)
            );
        }

        if (!class_exists('ZipArchive')){
            $errors[] = array(
                'text' => LANG_CP_INSTALL_NOT_ZIP,
                'hint' => LANG_CP_INSTALL_NOT_ZIP_HINT,
                'fix'  => LANG_CP_INSTALL_NOT_ZIP_FIX,
                'workaround' => sprintf(LANG_CP_INSTALL_NOT_ZIP_WA, $config->upload_root . $this->installer_upload_path),
            );
        }

        return $errors ? $errors : false;

    }

    private function checkManifestDepends($manifest){

        $results = array();

        if (isset($manifest['depends']['core'])){

            $need = (int)str_pad(str_replace('.', '', $manifest['depends']['core']), 6, '0');
            $has = (int)str_pad(str_replace('.', '', cmsCore::getVersion()), 6, '0');

            $results['core'] = ($need <= $has) ? true : false;

        }
        if (isset($manifest['depends']['package']) && isset($manifest['package']['installed_version'])){

            $need = (int)str_pad(str_replace('.', '', $manifest['depends']['package']), 6, '0');
            $has = (int)str_pad(str_replace('.', '', (string)$manifest['package']['installed_version']), 6, '0');

            $results['package'] = ($need <= $has) ? true : false;

        }
        if (isset($manifest['depends']['dependent_type']) && isset($manifest['depends']['dependent_name'])){

            $installed_version = call_user_func(array($this, $manifest['depends']['dependent_type'].'Installed'), array(
                'name'       => $manifest['depends']['dependent_name'],
                'controller' => (isset($manifest['depends']['dependent_controller']) ? $manifest['depends']['dependent_controller'] : null)
            ));

            $valid = $installed_version !== false;

            if($valid && isset($manifest['depends']['dependent_version'])){

                $need = (int)str_pad(str_replace('.', '', $manifest['depends']['dependent_version']), 6, '0');
                $has = (int)str_pad(str_replace('.', '', (string)$installed_version), 6, '0');

                $results['dependent_version'] = ($need <= $has) ? true : false;

            }

            $results['dependent_type'] = $valid;

        }

        return $results;

    }

    private function extractPackage($package_name){

        $zip_dir = cmsConfig::get('upload_path') . $this->installer_upload_path;
        $zip_file =  $zip_dir . '/' . $package_name;

        $zip = new ZipArchive();

        $res = $zip->open($zip_file);

        if ($res !== true){

            if(defined('LANG_ZIP_ERROR_'.$res)){
                $zip_error = constant('LANG_ZIP_ERROR_'.$res);
            } else {
                $zip_error = '';
            }

            cmsUser::addSessionMessage(LANG_CP_INSTALL_ZIP_ERROR.($zip_error ? ': '.$zip_error : ''), 'error');

            $this->redirectBack();

        }

        $zip->extractTo($zip_dir);
        $zip->close();

        unlink($zip_file);

        return true;

    }

    private function uploadPackage(){

        $uploader = new cmsUploader();

        if (!$uploader->isUploaded( $this->upload_name )){

            $last_error = $uploader->getLastError();
            if($last_error){
                cmsUser::addSessionMessage($last_error, 'error');
            }

            return false;

        }

        files_clear_directory(cmsConfig::get('upload_path') . $this->installer_upload_path);

        $result = $uploader->uploadForm($this->upload_name, $this->upload_exts, 0, $this->installer_upload_path);

        if (!$result['success']){
            cmsUser::addSessionMessage($result['error'], 'error');
            return false;
        }

        return $result['name'];

    }

}
