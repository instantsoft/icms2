<?php

class onCommentsUserTabInfo extends cmsAction {

    public function run($profile, $tab_name) {

        if (!empty($this->options['disable_icms_comments'])) {
            return false;
        }

        if (!$this->cms_user->isPrivacyAllowed($profile, 'view_user_comments')) {
            return false;
        }

        $is_moderator = $this->controller_moderation->userIsContentModerator($this->name, $this->cms_user->id);

        if ($profile['id'] == $this->cms_user->id || $is_moderator) {
            $this->model->disableApprovedFilter();
        }

        $this->count = $this->model->
                        filterEqual('user_id', $profile['id'])->
                        filterIsNull('is_deleted')->getCommentsCount();

        $this->model->resetFilters()->enableApprovedFilter();

        if (!$this->count) {
            return false;
        }

        return ['counter' => $this->count];
    }

}
