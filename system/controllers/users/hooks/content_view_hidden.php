<?php

class onUsersContentViewHidden extends cmsAction {

    public function run($data){

        $viewable     = $data['viewable'];
        $item         = $data['item'];
        $is_moderator = !empty($data['is_moderator']);
        $ctype        = !empty($data['ctype']) ? $data['ctype'] : array();

        if (!$viewable) { return $data; }

        if ($item['is_private'] == 1){

            if (!$this->cms_user->is_logged){ $data['viewable'] = false; return $data; }

            $is_friend           = $this->cms_user->isFriend($item['user_id']);
            $is_can_view_private = cmsUser::isAllowed($ctype['name'], 'view_all');

            if (!$is_friend && !$is_can_view_private && !$is_moderator){

                $data['access_text'] = sprintf(
                    LANG_CONTENT_PRIVATE_FRIEND_INFO,
                    (!empty($ctype['labels']['one']) ? string_ucfirst($ctype['labels']['one']) : LANG_PAGE),
                    href_to_profile($item['user']),
                    htmlspecialchars($item['user']['nickname'])
                );

                $data['viewable'] = false;

            }

        }

        return $data;

    }

}
