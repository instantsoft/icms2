<?php

class onPhotosContentAlbumsBeforeItem extends cmsAction {

    public function run($data){

        list($ctype, $album, $fields) = $data;

        if (cmsUser::isAllowed($ctype['name'], 'add')) {

            cmsTemplate::getInstance()->addToolButton(array(
                'class' => 'images',
                'title' => LANG_PHOTOS_UPLOAD,
                'href'  => href_to($this->name, 'upload', $album['id'])
            ));

        }

        return array($ctype, $album, $fields);

    }

}
