<?php

class onGroupsModerationList extends cmsAction {

    public function run($data){

        list($counts, $ctype_name, $page_url, $action) = $data;

        $ctypes_list = array_keys($counts);

        $exists = array_search($this->name, $ctypes_list);
        if($exists === false){ return false; }

        $list_html = '';

        if($ctype_name == $ctypes_list[$exists]){

            if($action == 'index'){
                $this->model->filterByModeratorTask($this->cms_user->id, $ctype_name, $this->cms_user->is_admin);
            } else
            if($action == 'waiting_list'){

                $this->model->filterEqual('owner_id', $this->cms_user->id);

                $this->model->filterByModeratorTask($this->cms_user->id, $ctype_name, true);

            } else
            if($action == 'draft'){

                $this->model->filterEqual('owner_id', $this->cms_user->id);
                $this->model->filterEqual('is_approved', 0);

                $this->model->select('IF(t.id IS NULL AND i.is_approved < 1, 1, NULL)', 'is_draft');

                $this->model->joinExcludingLeft('moderators_tasks', 't', 't.item_id', 'i.id', "t.ctype_name = '{$ctype_name}'");

            }

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
