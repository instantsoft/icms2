<?php

class onCommentsUserTabShow extends cmsAction {

    public function run($profile, $tab_name){

        $user = cmsUser::getInstance();
        $template = cmsTemplate::getInstance();

        $this->model->filterEqual('user_id', $profile['id']);

        $page_url = href_to('users', $profile['id'], 'comments');

        $list_html = $this->renderCommentsList($page_url);

        return $template->renderInternal($this, 'profile_tab', array(
            'user' => $user,
            'profile' => $profile,
            'html' => $list_html
        ));

    }

}
