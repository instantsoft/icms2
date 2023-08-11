<?php

class actionAdminInstall extends cmsAction {

    private $upload_name = 'package';
    private $upload_exts = 'zip,icp';

    public function run($do=false){

        // если нужно, передаем управление другому экшену
        if ($do){
            $this->runExternalAction('install_'.$do, array_slice($this->params, 1));
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

        if(!$manifest){
            $this->redirectToAction('install');
        }

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

            if(version_compare($package_v, $manifest['package']['installed_version']) == -1){

                files_clear_directory(cmsConfig::get('upload_path') . $this->installer_upload_path);

                cmsUser::addSessionMessage(sprintf(LANG_CP_PACKAGE_UPDATE_ERROR, $manifest['package']['type_hint'], $manifest['info']['title'], $package_v, $manifest['package']['installed_version']), 'error');

                $this->redirectToAction('install');

            }

            if(version_compare($package_v, $manifest['package']['installed_version']) == 0){

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

        if (!function_exists('parse_ini_file')){
            $errors[] = array(
                'text' => LANG_CP_INSTALL_NOT_PARSE_INI_FILE,
                'hint' => LANG_CP_INSTALL_NOT_PARSE_INI_FILE_HINT,
                'fix'  => LANG_CP_INSTALL_NOT_PARSE_INI_FILE_FIX
            );
        }

        return $errors ? $errors : false;

    }

    private function checkManifestDepends($manifest){

        $results = array();

        if (isset($manifest['depends']['core'])){

            $results['core'] = (version_compare(cmsCore::getVersion(), $manifest['depends']['core']) >= 0) ? true : false;

        }
        if (isset($manifest['depends']['package']) && isset($manifest['package']['installed_version'])){

            $results['package'] = (version_compare((string)$manifest['package']['installed_version'], $manifest['depends']['package']) >= 0) ? true : false;

        }
        if (isset($manifest['depends']['dependent_type']) && isset($manifest['depends']['dependent_name'])){

            $installed_version = call_user_func(array($this, $manifest['depends']['dependent_type'].'Installed'), array(
                'name'       => $manifest['depends']['dependent_name'],
                'controller' => (isset($manifest['depends']['dependent_controller']) ? $manifest['depends']['dependent_controller'] : null)
            ));

            $valid = $installed_version !== false;

            if($valid && isset($manifest['depends']['dependent_version'])){

                $results['dependent_version'] = (version_compare((string)$installed_version, $manifest['depends']['dependent_version']) >= 0) ? true : false;

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

        // прописываем id дополнения в манифест, если установка из каталога
        // и id дополнения передано
        $addon_id = $this->request->get('addon_id', 0);
        $path = $this->cms_config->upload_path . $this->installer_upload_path;
        $ini_file = $path . '/' . "manifest.{$this->cms_config->language}.ini";
        $ini_file_default = $path . '/manifest.ru.ini';
        if (!file_exists($ini_file)){ $ini_file = $ini_file_default; }

        if (file_exists($ini_file) && $addon_id){

            $manifest = parse_ini_file($ini_file, true);

            if(!empty($manifest['info']['addon_id'])){
                return true;
            }

            $ini = '';

            $manifest['info']['addon_id'] = $addon_id;

            $section_names = array_keys($manifest);

            $encodeValue = function ($value){
                if (is_bool($value)) {
                    return (int)$value;
                }
                if (is_string($value)) {
                    return "\"$value\"";
                }
                return $value;
            };

            foreach ($section_names as $section_name) {

                $section = $manifest[$section_name];

                if (empty($section) || !is_array($section)) {
                    continue;
                }

                $ini .= "[$section_name]\n";

                foreach ($section as $option => $value) {
                    if (is_numeric($option)) {
                        $option = $section_name;
                        $value = array($value);
                    }
                    if (is_array($value)) {
                        foreach ($value as $currentValue) {
                            $ini .= $option . '[] = '.$encodeValue($currentValue)."\n";
                        }
                    } else {
                        $ini .= $option.' = '.$encodeValue($value)."\n";
                    }
                }

                $ini .= "\n";

            }

            file_put_contents($ini_file, $ini);

        }

        return true;

    }

    private function uploadPackage(){

        $this->cms_uploader->enableRemoteUpload();

        if (!$this->cms_uploader->isUploaded($this->upload_name) && !$this->cms_uploader->isUploadedFromLink($this->upload_name)){

            $last_error = $this->cms_uploader->getLastError();
            if($last_error){
                cmsUser::addSessionMessage($last_error, 'error');
            }

            return false;

        }

        files_clear_directory(cmsConfig::get('upload_path') . $this->installer_upload_path);

        $result = $this->cms_uploader->setAllowedMime([
            'application/zip'
        ])->upload($this->upload_name, $this->upload_exts, 0, $this->installer_upload_path);

        if (!$result['success']){
            cmsUser::addSessionMessage($result['error'], 'error');
            return false;
        }

        return $result['name'];
    }

}
