<?php

class actionModerationIndex extends cmsAction {

    public function run($ctype_name=false){

        $counts = $this->model->getTasksCounts($this->cms_user->id, $this->cms_user->is_admin);

        $is_moderator = $this->model->isUserModerator($this->cms_user->id) || $this->cms_user->is_admin;
        if (!$is_moderator) { cmsCore::error404(); }

        if (!$counts){
            return $this->cms_template->render('empty');
        }

        $is_index = false;

        $ctypes_list = array_keys($counts);

        if (!$ctype_name) { $ctype_name = $ctypes_list[0]; $is_index = true; }

        $content_controller = cmsCore::getController('content', $this->request);

        $ctypes = $content_controller->model->filterIn('name', $ctypes_list)->getContentTypesFiltered();
        $ctypes = array_collection_to_list($ctypes, 'name', 'title');

        $ctype = $content_controller->model->getContentTypeByName($ctype_name);

        $content_controller->model->filterByModeratorTask($this->cms_user->id, $ctype_name, $this->cms_user->is_admin);

        $page_url = $is_index ? href_to($this->name) : href_to($this->name, 'index', $ctype_name);

        $content_controller->model->disableApprovedFilter()->disablePubFilter()->disablePrivacyFilter()->disableDeleteFilter();

        $list_html = $content_controller->renderItemsList($ctype, $page_url, true);

        return $this->cms_template->render('index', array(
            'is_index'   => $is_index,
            'counts'     => $counts,
            'ctype'      => $ctype,
            'ctypes'     => $ctypes,
            'ctype_name' => $ctype_name,
            'list_html'  => $list_html,
            'user'       => $this->cms_user
        ));

    }

}
