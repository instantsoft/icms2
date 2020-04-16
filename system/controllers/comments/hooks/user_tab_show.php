<?php

class onCommentsUserTabShow extends cmsAction {

    public function run($profile, $tab_name, $tab){

        $this->model->filterEqual('user_id', $profile['id']);

        if($profile['id'] == $this->cms_user->id || $this->cms_user->is_admin || cmsCore::getModel('moderation')->userIsContentModerator($this->name, $this->cms_user->id)){

            $this->model->disableApprovedFilter();

            $this->model->orderByList(array(
                array('by' => 'is_approved', 'to' => 'desc'),
                array('by' => 'date_pub', 'to' => 'desc')
            ));

        }

        $page_url = href_to('users', $profile['id'], 'comments');

        $list_html = $this->renderCommentsList($page_url);

        $this->model->enableApprovedFilter();

        return $this->cms_template->renderInternal($this, 'profile_tab', array(
            'tab'     => $tab,
            'user'    => $this->cms_user,
            'profile' => $profile,
            'html'    => $list_html
        ));

    }

}
