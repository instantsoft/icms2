<?php

class onContentModerationList extends cmsAction {

    public function run($data){

        list($counts, $ctype_name, $page_url) = $data;

        $ctypes_list = array_keys($counts);

        $ctypes = $this->model->filterIn('name', $ctypes_list)->getContentTypesFiltered();
        if(!$ctypes){ return false; }

        $ctypes = array_collection_to_list($ctypes, 'name', 'title');

        $list_html = '';

        if(isset($ctypes[$ctype_name])){

            $ctype = $this->model->getContentTypeByName($ctype_name);

            $this->model->filterByModeratorTask($this->cms_user->id, $ctype_name, $this->cms_user->is_admin);

            $this->model->disableApprovedFilter()->disablePubFilter()->disablePrivacyFilter()->disableDeleteFilter();

            $list_html = $this->disableCheckListPerm()->setListContext('moderation_list')->renderItemsList($ctype, $page_url, true);

        }

        return array(
            'name'      => $this->name,
            'titles'    => $ctypes,
            'list_html' => $list_html
        );

    }

}
