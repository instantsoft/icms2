<?php

class actionAdminWidgetsUnbindAllWidgets extends cmsAction {

    public function run(){

        cmsCore::getModel('widgets')->updateFiltered();

        $this->redirectToAction('widgets');

    }

}
