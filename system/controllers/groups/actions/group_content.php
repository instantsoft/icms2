<?php

class actionGroupsGroupContent extends cmsAction {

    public $lock_explicit_call = true;

    public function run($group, $ctype_name = false){

        if (!$ctype_name) { cmsCore::error404(); }

        $content_controller = cmsCore::getController('content', $this->request);

        $ctype = $content_controller->model->getContentTypeByName($ctype_name);
        if (!$ctype || empty($ctype['is_in_groups'])) { cmsCore::error404(); }

        $content_controller->model->
                filterEqual('parent_id', $group['id'])->
                filterEqual('parent_type', 'group')->
                orderBy('date_pub', 'desc')->forceIndex('parent_id');

        $page_url = href_to($this->name, $group['slug'], array('content', $ctype_name));

        if (($this->cms_user->id == $group['owner_id']) || $this->cms_user->is_admin){
            $content_controller->model->disableApprovedFilter();
			$content_controller->model->disablePubFilter();
            $content_controller->model->disablePrivacyFilter();
        }

        $this->filterPrivacyGroupsContent($ctype, $content_controller->model, $group);

        $group['sub_title'] = empty($ctype['labels']['profile']) ? $ctype['title'] : $ctype['labels']['profile'];

        $this->cms_template->addBreadcrumb(LANG_GROUPS, href_to('groups'));
        $this->cms_template->addBreadcrumb($group['title'], href_to('groups', $group['slug']));
        $this->cms_template->addBreadcrumb($group['sub_title']);

        return $this->cms_template->render('group_content', array(
            'user'  => $this->cms_user,
            'group' => $group,
            'ctype' => $ctype,
            'html'  => $content_controller->renderItemsList($ctype, $page_url)
        ));

    }

}
