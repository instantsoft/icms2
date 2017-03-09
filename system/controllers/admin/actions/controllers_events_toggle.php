<?php

class actionAdminControllersEventsToggle extends cmsAction {

    public function run($id=false){

        if (!$id){
            cmsTemplate::getInstance()->renderJSON(array(
                'error' => true,
            ));
        }

        $item = $this->model->getItemByField('events', 'id', $id);

        if (!$item){
            cmsTemplate::getInstance()->renderJSON(array(
                'error' => true,
            ));
        }

        $is_pub = $item['is_enabled'] ? false : true;

        $this->model->update('events', $id, array(
            'is_enabled' => $is_pub
        ));

        cmsCache::getInstance()->clean('events');

        $this->cms_template->renderJSON(array(
            'error' => false,
            'is_on' => $is_pub
        ));

    }

}