<?php

class onContentModerationList extends cmsAction {

    public function run($data){

        list($counts, $ctype_name, $page_url, $action) = $data;

        $ctypes_list = array_keys($counts);

        $ctypes = $this->model->filterIn('name', $ctypes_list)->getContentTypesFiltered();
        if(!$ctypes){ return false; }

        $ctypes = array_collection_to_list($ctypes, 'name', 'title');

        $list_html = '';

        if(isset($ctypes[$ctype_name])){

            $ctype = $this->model->getContentTypeByName($ctype_name);

            if($action == 'index'){
                $this->model->filterByModeratorTask($this->cms_user->id, $ctype_name, $this->cms_user->is_admin);
            } else
            if($action == 'waiting_list'){

                $this->model->filterEqual('user_id', $this->cms_user->id);

                $this->model->filterByModeratorTask($this->cms_user->id, $ctype_name, true);

            } else
            if($action == 'draft'){

                $this->model->filterEqual('user_id', $this->cms_user->id);
                $this->model->filterEqual('is_approved', 0);

                $this->model->select('IF(t.id IS NULL AND i.is_approved < 1, 1, NULL)', 'is_draft');

                $this->model->joinExcludingLeft('moderators_tasks', 't', 't.item_id', 'i.id', "t.ctype_name = '{$ctype_name}'");

            }

            $this->model->disableApprovedFilter()->disablePubFilter()->disablePrivacyFilter()->disableDeleteFilter();

            $list_html = $this->disableCheckListPerm()->setListContext('moderation_list')->renderItemsList($ctype, $page_url);

        }

        return array(
            'name'      => $this->name,
            'titles'    => $ctypes,
            'list_html' => $list_html
        );

    }

}
