<?php

class actionAdminPackageFilesList extends cmsAction {

    private $types = array('widgets', 'controllers');

    public function run($type = false, $id = false, $hide_delete_hint = true){

        if (!$this->request->isAjax()){ cmsCore::error404(); }

        if (!$type || !in_array($type, $this->types)){
            cmsCore::error404();
        }

        $item = $this->model->filterEqual('id', $id)->getItem($type);
        if (!$item){ cmsCore::error404(); }

        $item['files'] = cmsModel::yamlToArray($item['files']);

        return $this->cms_template->render('install_package_files', array(
            'type'              => 'controllers',
            'hide_title'        => true,
            'hide_continue_btn' => true,
            'hide_delete_hint'  => $hide_delete_hint,
            'files'             => $item['files']
        ));

    }

}
