<?php

class actionModerationIndex extends cmsAction {

    public function run($ctype_name = false){

        $counts = $this->model->getTasksCounts($this->cms_user->id, $this->cms_user->is_admin);

        $is_moderator = $this->model->isUserModerator($this->cms_user->id) || $this->cms_user->is_admin;
        if (!$is_moderator) { cmsCore::error404(); }

        if (!$counts){
            return $this->cms_template->render('empty', array(
                'page_title' => LANG_MODERATION,
                'empty_hint' => LANG_MODERATION_NO_TASKS
            ));
        }

        $is_index = false;

        $ctypes_list = array_keys($counts);

        if (!$ctype_name) { $ctype_name = $ctypes_list[0]; $is_index = true; }

        $page_url = $is_index ? href_to($this->name) : href_to($this->name, 'index', $ctype_name);

        $titles = array(); $list_html = '';

        $moderations = cmsEventsManager::hookAll('moderation_list', array($counts, $ctype_name, $page_url, 'index'), array(), $this->request);

        foreach ($moderations as $moderation) {

            $titles = array_merge($titles, $moderation['titles']);

            if(!empty($moderation['list_html'])){
                $list_html = $moderation['list_html'];
            }

        }

        if(!isset($titles[$ctype_name])){ cmsCore::error404(); }

        if (!$is_index){

            $this->cms_template->addBreadcrumb(LANG_MODERATION, href_to($this->name));
            $this->cms_template->addBreadcrumb($titles[$ctype_name]);

            $this->cms_template->setPageTitle(LANG_MODERATION, $titles[$ctype_name]);

        } else {

            $this->cms_template->setPageTitle(LANG_MODERATION);

            $this->cms_template->addBreadcrumb(LANG_MODERATION);

        }

        $content_menu = array();

        $is_first = true;

        foreach($counts as $c_name => $count){
            $content_menu[] = array(
                'title'   => $titles[$c_name],
                'url'     => $is_first ? href_to($this->name) : href_to($this->name, 'index', $c_name),
                'counter' => $count
            );
            $is_first = false;
        }

        $this->cms_template->addMenuItems('moderation_content_types', $content_menu);

        return $this->cms_template->render('index', array(
            'list_html' => $list_html,
            'page_title' => LANG_MODERATION
        ));

    }

}
