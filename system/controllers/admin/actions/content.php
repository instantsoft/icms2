<?php

class actionAdminContent extends cmsAction {

    public function run($do=false){

        // если нужно, передаем управление другому экшену
        if ($do){
            $this->runAction('content_'.$do, array_slice($this->params, 1));
            return;
        }

        $content_model = cmsCore::getModel('content');

        $ctypes = $content_model->getContentTypes();

        $grid = $this->loadDataGrid('content_items');

        return cmsTemplate::getInstance()->render('content', array(
            'ctypes' => $ctypes,
            'grid' => $grid
        ));

    }

}
