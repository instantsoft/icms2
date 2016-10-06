<?php

class actionGroupsGroupContent extends cmsAction {

    public $lock_explicit_call = true;

    public function run($group, $ctype_name=false){

        if (!$ctype_name) { cmsCore::error404(); }

        $content_controller = cmsCore::getController('content', $this->request);

        $ctype = $content_controller->model->getContentTypeByName($ctype_name);
        if (!$ctype) { cmsCore::error404(); }

        $content_controller->model->
                filterEqual('parent_id', $group['id'])->
                filterEqual('parent_type', 'group')->
                orderBy('date_pub', 'desc')->forceIndex('parent_id');

        $page_url = href_to($this->name, $group['id'], array('content', $ctype_name));

        $html = $content_controller->renderItemsList($ctype, $page_url);

        return $this->cms_template->render('group_content', array(
            'user'  => $this->cms_user,
            'group' => $group,
            'ctype' => $ctype,
            'html'  => $html
        ));

    }

}