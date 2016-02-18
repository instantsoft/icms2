<?php

class actionAdminWidgetsHide extends cmsAction {

    public function run($id=false){

        if (!$id){ cmsCore::error404(); }
        
		cmsCore::getModel('widgets')->hideWidget($id);

        $this->redirectToAction('widgets');

    }

}
