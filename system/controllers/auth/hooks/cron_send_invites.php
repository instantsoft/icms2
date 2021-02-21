<?php

class onAuthCronSendInvites extends cmsAction {

    public $disallow_event_db_register = true;

    public function run() {

        if (!$this->options['is_reg_invites']) {
            return false;
        }

        if (!$this->options['is_invites']) {
            return false;
        }

        cmsCore::loadControllerLanguage('users');

        $users_model = cmsCore::getModel('users');

        $users_model->filterIsNull('is_locked');

        $users_model->
                filterStart()->
                filterDateOlder('date_invites', $this->options['invites_period'])->
                filterOr()->
                filterIsNull('date_invites')->
                filterEnd();

        $users_model->filterGtEqual('karma', $this->options['invites_min_karma']);
        $users_model->filterGtEqual('rating', $this->options['invites_min_rating']);

        if ($this->options['invites_min_days']) {
            $users_model->filterDateOlder('date_reg', $this->options['invites_min_days']);
        }

        $users = $users_model->getUsers();
        if (!$users) { return false; }

        foreach ($users as $user) {

            $this->model->addInvites($user['id'], $this->options['invites_qty']);

            $this->model_messages->addNotice([$user['id']], [
                'content' => sprintf(LANG_AUTH_INVITE_SEND_COUNT, html_spellcount($this->options['invites_qty'], LANG_USERS_INVITES_SPELLCOUNT))
            ]);
        }

        return true;
    }

}
