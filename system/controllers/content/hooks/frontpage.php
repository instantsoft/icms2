<?php

class onContentFrontpage extends cmsAction {

    public function run($action){

        $this->request->set('ctype_name', $action);
        $this->request->set('slug', 'index');

        return $this->runAction('category_view');

    }

}
