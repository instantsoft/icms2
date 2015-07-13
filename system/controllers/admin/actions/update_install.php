<?php

class actionAdminUpdateInstall extends cmsAction {

    public function run($do=false){

        $updater = new cmsUpdater();

        $update = $updater->checkUpdate();

        if ($update == cmsUpdater::UPDATE_NOT_AVAILABLE){
            cmsUser::addSessionMessage(LANG_CP_UPDATE_NOT_AVAILABLE);
            $this->redirectToAction('update');
        }

        if ($update == cmsUpdater::UPDATE_CHECK_ERROR || empty($update['version'])){
            cmsUser::addSessionMessage(LANG_CP_UPDATE_CHECK_FAIL, 'error');
            $this->redirectToAction('update');
        }

        if (!function_exists('curl_init')){
            cmsUser::addSessionMessage(LANG_CP_UPDATE_DOWNLOAD_FAIL, 'error');
            $this->redirectToAction('update');
        }

        $url = $update['url'];
        $package_name = basename($url);
        $destination = cmsConfig::get('upload_path') . 'installer/' . $package_name;

        $result = file_save_from_url($url, $destination);

        if ($result === false){
            cmsUser::addSessionMessage(LANG_CP_UPDATE_DOWNLOAD_FAIL, 'error');
            $this->redirectToAction('update');
        }

        $this->redirectToAction('install', false, array('package_name' => $package_name));

    }

}
