<?php

class actionGroupsGroupActivity extends cmsAction {

    public $lock_explicit_call = true;

    public function run($group){

        $activity_controller = cmsCore::getController('activity', $this->request);

        $activity_controller->model->filterEqual('group_id', $group['id']);

        $page_url = href_to($this->name, $group['slug'], 'activity');

        $group['sub_title'] = LANG_GROUPS_PROFILE_ACTIVITY;

        $this->cms_template->addBreadcrumb(LANG_GROUPS, href_to('groups'));
        $this->cms_template->addBreadcrumb($group['title'], href_to('groups', $group['slug']));
        $this->cms_template->addBreadcrumb($group['sub_title']);

        return $this->cms_template->render('group_activity', array(
            'user'  => $this->cms_user,
            'group' => $group,
            'html'  => $activity_controller->renderActivityList($page_url)
        ));

    }

}
