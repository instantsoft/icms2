<?php

class onContentAdminSubscriptionsList extends cmsAction {

    public function run($items){

        if($items){
            foreach ($items as $key => $item) {

                if($item['controller'] != 'content'){ continue; }

                $ctype = $this->model->getContentTypeByName($item['subject']);

                if($ctype){
                    $items[$key]['subject'] = $ctype['title'];
                }

            }
        }

        return $items;

    }

}
