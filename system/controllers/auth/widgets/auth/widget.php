<?php
class widgetAuthAuth extends cmsWidget {

	public $is_cacheable = false;

    public function run(){

        if (cmsUser::isLogged()){ return false; }

        return array(
            'hooks_html' => cmsEventsManager::hookAll('login_form_html')
        );

    }

}
