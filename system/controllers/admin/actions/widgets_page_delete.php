<?php

class actionAdminWidgetsPageDelete extends cmsAction {

    public function run($id=false){

        if (!$id) { cmsCore::error404(); }

        $widgets_model = cmsCore::getModel('widgets');
        
        $widgets_model->deletePage($id);

        cmsUser::unsetCookie('widgets_tree_path');
        
        $this->redirectBack();
        
    }

}
