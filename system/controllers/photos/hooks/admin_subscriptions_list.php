<?php

class onPhotosAdminSubscriptionsList extends cmsAction {

    public function run($items){

        if($items){
            foreach ($items as $key => $item) {

                if($item['controller'] != 'photos'){ continue; }

                $items[$key]['subject'] = LANG_PHOTOS_ALBUM;

            }
        }

        return $items;

    }

}
