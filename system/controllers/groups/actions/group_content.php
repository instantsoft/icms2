<?php

class actionGroupsGroupContent extends cmsAction {

    public function run($group, $ctype_name=false){

        if (!$ctype_name) { cmsCore::error404(); }

        $user = cmsUser::getInstance();

        $content_controller = cmsCore::getController('content', $this->request);

        $ctype = $content_controller->model->getContentTypeByName($ctype_name);
        if (!$ctype) { cmsCore::error404(); }

        $content_controller->model->filterEqual('parent_id', $group['id'])->filterEqual('parent_type', 'group');

        $page_url = href_to($this->name, $group['id'], array('content', $ctype_name));

        $html = $content_controller->renderItemsList($ctype, $page_url);

        return cmsTemplate::getInstance()->render('group_content', array(
            'user' => $user,
            'group' => $group,
            'ctype' => $ctype,
            'html' => $html
        ));

    }

}
