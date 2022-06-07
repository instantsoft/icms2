<?php

class onCommentsUserTabShow extends cmsAction {

    public function run($profile, $tab_name, $tab) {

        $this->model->filterEqual('user_id', $profile['id']);

        $is_moderator = $this->controller_moderation->userIsContentModerator($this->name, $this->cms_user->id);

        if ($profile['id'] == $this->cms_user->id || $is_moderator) {

            $this->model->disableApprovedFilter();

            $this->model->orderByList([
                ['by' => 'is_approved', 'to' => 'desc'],
                ['by' => 'date_pub', 'to' => 'desc']
            ]);
        }

        $page_url = href_to_profile($profile, 'comments');

        $list_html = $this->renderCommentsList($page_url);

        $this->model->enableApprovedFilter();

        return $this->cms_template->renderInternal($this, 'profile_tab', [
            'tab'     => $tab,
            'user'    => $this->cms_user,
            'profile' => $profile,
            'html'    => $list_html
        ]);
    }

}
