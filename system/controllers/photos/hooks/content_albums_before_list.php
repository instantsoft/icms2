<?php

class onPhotosContentAlbumsBeforeList extends cmsAction {

    public function run($data){

        list($ctype, $items) = $data;

        if (cmsUser::isAllowed($ctype['name'], 'add')) {

            cmsTemplate::getInstance()->addToolButton(array(
                'class' => 'images',
                'title' => LANG_PHOTOS_UPLOAD,
                'href'  => href_to($this->name, 'upload')
            ));

        }

        return $data;

    }

}
