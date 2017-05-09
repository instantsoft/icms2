<?php

class onGroupsContentViewHidden extends cmsAction {

    public function run($data){

        $viewable     = $data['viewable'];
        $item         = $data['item'];
        $is_moderator = !empty($data['is_moderator']);
        $ctype        = !empty($data['ctype']) ? $data['ctype'] : array();

        if (!$viewable) { return $data; }

        if ($item['parent_type'] != 'group') { return $data; }

        if ($item['is_parent_hidden'] || in_array($item['is_private'], array(3, 4))){

            if (!$this->cms_user->is_logged){ $data['viewable'] = false; return $data; }

            $membership = $this->model->getMembership($item['parent_id'], $this->cms_user->id) || $this->cms_user->is_admin || $is_moderator;

            if ($membership === false || ($item['is_private'] == 4 && !cmsUser::isAllowed($ctype['name'], 'add'))){

                $group = $this->model->getGroup($item['parent_id']);

                if($group){

                    if($membership){
                        $data['access_text'] = LANG_ACCESS_DENIED;
                    } else {
                        $data['access_text'] = sprintf(LANG_GROUPS_CTYPE_ACCESS, href_to('groups', $group['slug']), $group['title']);
                    }

                    $data['access_redirect_url'] = href_to('groups', $group['slug']);

                    $data['viewable'] = false;

                    return $data;

                }

            }

        }

        return $data;

    }

}
