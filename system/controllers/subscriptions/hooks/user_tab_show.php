<?php

class onSubscriptionsUserTabShow extends cmsAction {

    public function run($profile, $tab_name, $tab){

        $html = $this->renderSubscriptionsList(href_to_profile($profile, array('subscriptions')), $this->request->get('page', 1));

        return $this->cms_template->renderInternal($this, 'profile_tab', array(
            'user'    => $this->cms_user,
            'tab'     => $tab,
            'html'    => $html,
            'profile' => $profile
        ));

    }

}
