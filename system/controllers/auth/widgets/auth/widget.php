<?php
class widgetAuthAuth extends cmsWidget {

	public $is_cacheable = false;

    public function run(){

        if (cmsUser::isLogged()){ return false; }

        $auth = cmsCore::getController('auth');

        $form = $auth->getForm('login');

        return array(
            'form' => $form,
            'hooks_html' => cmsEventsManager::hookAll('login_form_html')
        );

    }

}
