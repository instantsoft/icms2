<?php

class actionAdminWidgetsUnbindAllWidgets extends cmsAction {

    public function run($template_name=null){

        if($template_name){
            cmsCore::getModel('widgets')->unbindAllWidgets($template_name);
        }

        $this->redirectBack();

    }

}
