<?php

class onAuthCronSendInvites extends cmsAction {

    public function run(){

        if (!$this->options['is_reg_invites']) { return false; }
        if (!$this->options['is_invites']) { return false; }

        $period = $this->options['invites_period'];
        $qty = $this->options['invites_qty'];
        $min_karma = $this->options['invites_min_karma'];
        $min_rating = $this->options['invites_min_rating'];
        $min_days = $this->options['invites_min_days'];

        $users_model = cmsCore::getModel('users');

        $users_model->filterIsNull('is_locked');

        $users_model->
                    filterStart()->
                        filterDateOlder('date_invites', $period)->
                        filterOr()->
                        filterIsNull('date_invites')->
                    filterEnd();

        $users_model->filterGtEqual('karma', $min_karma);
        $users_model->filterGtEqual('rating', $min_rating);
        $users_model->filterDateOlder('date_reg', $min_days);

        $users = $users_model->getUsers();
        if (!$users) { return false; }

        foreach($users as $user){

            $this->model->addInvites($user['id'], $qty);

        }

    }

}
