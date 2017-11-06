<?php

class onGroupsModerationList extends cmsAction {

    public function run($data){

        list($counts, $ctype_name, $page_url) = $data;

        $ctypes_list = array_keys($counts);

        $exists = array_search($this->name, $ctypes_list);
        if($exists === false){ return false; }

        $list_html = '';

        if($ctype_name == $ctypes_list[$exists]){

            $this->model->filterByModeratorTask($this->cms_user->id, $this->cms_user->is_admin);

            $this->model->disableApprovedFilter();

            $list_html = $this->renderGroupsList($page_url);

        }

        return array(
            'name'      => $this->name,
            'titles'    => array(
                $this->name => LANG_GROUPS
            ),
            'list_html' => $list_html
        );

    }

}
