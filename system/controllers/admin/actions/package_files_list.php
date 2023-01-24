<?php

class actionAdminPackageFilesList extends cmsAction {

    private $types = ['widgets', 'controllers'];

    public function run($type = false, $id = false, $hide_delete_hint = true) {

        if (!$this->request->isAjax()) {
            return cmsCore::error404();
        }

        if (!$type || !in_array($type, $this->types)) {
            return cmsCore::error404();
        }

        $item = $this->model->filterEqual('id', $id)->getItem($type);
        if (!$item) {
            return cmsCore::error404();
        }

        $item['files'] = cmsModel::yamlToArray($item['files']);

        return $this->cms_template->render('install_package_files', [
            'type'              => 'controllers',
            'hide_title'        => true,
            'hide_continue_btn' => true,
            'hide_delete_hint'  => $hide_delete_hint,
            'files'             => $item['files']
        ]);
    }

}
