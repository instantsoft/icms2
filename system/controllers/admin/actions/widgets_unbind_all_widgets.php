<?php

class actionAdminWidgetsUnbindAllWidgets extends cmsAction {

    public function run(){

        cmsCore::getModel('widgets')->unbindAllWidgets();

        $this->redirectToAction('widgets');

    }

}
