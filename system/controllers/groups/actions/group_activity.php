<?php

class actionGroupsGroupActivity extends cmsAction {

    public $lock_explicit_call = true;

    public function run($group){

        $activity_controller = cmsCore::getController('activity', $this->request);

        $activity_controller->model->filterEqual('group_id', $group['id']);

        $page_url = href_to($this->name, $group['id'], 'activity');

        $html = $activity_controller->renderActivityList($page_url);

        return cmsTemplate::getInstance()->render('group_activity', array(
            'user'  => $this->cms_user,
            'group' => $group,
            'html'  => $html
        ));

    }

}
