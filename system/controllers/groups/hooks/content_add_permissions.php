<?php

class onGroupsContentAddPermissions extends cmsAction {

    public function run($data){

        if (!$data['ctype']['is_in_groups'] && !$data['ctype']['is_in_groups_only']){
            return $data;
        }

        $group_count = $this->model->filterByMember($this->cms_user->id)->getGroupsCount();
        $this->model->resetFilters();

        if (!$group_count && !empty($data['ctype']['is_in_groups_only'])){
            cmsUser::addSessionMessage(LANG_CONTENT_IS_IN_GROUPS_ONLY, 'error');
            $this->redirectBack();
        }

        $group_id = $this->cms_core->request->get('group_id', 0);
        if(!$group_id){ return $data; }

        $group = $this->model->getGroup($group_id);
        if(!$group){ return $data; }

        $group['access'] = $this->getGroupAccess($group);

        $data['can_add'] = $this->isContentAddAllowed($data['ctype']['name'], $group);

        return $data;

    }

}
