<?php

class backendManifests extends cmsBackend{

    public function actionIndex(){
        $this->redirectToAction('manifests');
    }

    public function getHooksInDB(){

        $hooks_in_db = array();

        $controllers_manifests = cmsDatabase::getInstance()->getRows('controllers_hooks', '1', '*', 'name ASC, ordering ASC');

        foreach($controllers_manifests as $controller){

            $hooks_in_db[ $controller['controller'] ][] = $controller['name'];

        }

        return $hooks_in_db;

    }

    public function getHooksInFiles(){

        $hooks_in_files = array();

        $controllers = cmsCore::getDirsList('system/controllers', true);

        foreach($controllers as $controller_name){

            $manifest_file = cmsConfig::get('root_path') . 'system/controllers/' . $controller_name . '/manifest.php';

            if (!file_exists($manifest_file)){ continue; }

            $manifest = include $manifest_file;

            if (!$manifest || empty($manifest['hooks'])) { continue; }

            $hooks_in_files[ $controller_name ] = $manifest['hooks'];

        }

        return $hooks_in_files;

    }

}