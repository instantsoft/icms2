<?php

class onActivityContentGroupsAfterDelete extends cmsAction {

    public function run($group) {

        $this->model->filterEqual('group_id', $group['id'])->deleteFiltered('activity');

        cmsCache::getInstance()->clean('activity.entries');

        return $group;
    }

}
