<?php

class onCommentsUserTabShow extends cmsAction {

    public function run($profile, $tab_name, $tab){

        $this->model->filterEqual('user_id', $profile['id']);

        $page_url = href_to('users', $profile['id'], 'comments');

        $list_html = $this->renderCommentsList($page_url);

        return $this->cms_template->renderInternal($this, 'profile_tab', array(
            'tab'     => $tab,
            'user'    => $this->cms_user,
            'profile' => $profile,
            'html'    => $list_html
        ));

    }

}
