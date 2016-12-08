<?php

class onGroupsContentViewHidden extends cmsAction {

    public function run($data){

        $viewable     = $data['viewable'];
        $item         = $data['item'];
        $is_moderator = !empty($data['is_moderator']);

        if (!$viewable) { return $data; }

        if (!$item['parent_type'] == 'group') { return $data; }

        if (!$this->cms_user->is_logged){ $data['viewable'] = false; return $data; }

        $membership = $this->model->getMembership($item['parent_id'], $this->cms_user->id) || $this->cms_user->is_admin || $is_moderator;

        if ($membership === false){

            $group = $this->model->getGroup($item['parent_id']);

            if($group){

                $data['access_text'] = sprintf(LANG_GROUPS_CTYPE_ACCESS, href_to('groups', $group['id']), $group['title']);

                $data['access_redirect_url'] = href_to('groups', $group['id']);

                $data['viewable'] = false;

                return $data;

            }

        }

        return $data;

    }

}
