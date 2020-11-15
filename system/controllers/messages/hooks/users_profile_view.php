<?php

class onMessagesUsersProfileView extends cmsAction {

    public function run($profile) {

        if (!$this->cms_user->is_logged) {
            return $profile;
        }

        if (empty($this->options['is_enable_pm'])) {
            return $profile;
        }

        if ($this->cms_user->id != $profile['id'] && !$profile['is_deleted'] && !$profile['is_locked']) {

            $this->cms_template->addToolButton([
                'title' => LANG_PM_SEND_TO_USER,
                'class' => 'messages ajax-modal', 'icon'  => 'envelope-open-text',
                'href'  => href_to($this->name, 'write', $profile['id'])
            ]);
        }

        return $profile;
    }

}
