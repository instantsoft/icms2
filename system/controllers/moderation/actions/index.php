<?php

class actionModerationIndex extends cmsAction {

    public function run($ctype_name=false){

        $user = cmsUser::getInstance();
        $template = cmsTemplate::getInstance();

        $counts = $this->model->getTasksCounts($user->id);

        $is_moderator = $this->model->isUserModerator($user->id);

        if (!$is_moderator) { cmsCore::error404(); }

        if (!$counts){
            return $template->render('empty');
        }

        $is_index = false;

        $ctypes_list = array_keys($counts);

        if (!$ctype_name) { $ctype_name = $ctypes_list[0]; $is_index = true; }

        $content_controller = cmsCore::getController('content');

        $ctypes = $content_controller->model->filterIn('name', $ctypes_list)->getContentTypes();
        $ctypes = array_collection_to_list($ctypes, 'name', 'title');

        $ctype = $content_controller->model->getContentTypeByName($ctype_name);

        $content_controller->model->filterByModeratorTask($user->id, $ctype_name);

        $page_url = $is_index ? href_to($this->name) : href_to($this->name, $ctype_name);

        $content_controller->model->disableApprovedFilter();

        $list_html = $content_controller->renderItemsList($ctype, $page_url, true);

        return $template->render('index', array(
            'is_index' => $is_index,
            'counts' => $counts,
            'ctype' => $ctype,
            'ctypes' => $ctypes,
            'ctype_name' => $ctype_name,
            'list_html' => $list_html,
            'user' => $user
        ));

    }

}
