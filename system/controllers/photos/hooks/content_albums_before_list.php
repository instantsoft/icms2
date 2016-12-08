<?php

class onPhotosContentAlbumsBeforeList extends cmsAction {

    public function run($data){

        list($ctype, $items) = $data;

        if (cmsUser::isAllowed($ctype['name'], 'add')) {

            $this->cms_template->addToolButton(array(
                'class' => 'images',
                'title' => LANG_PHOTOS_UPLOAD,
                'href'  => href_to($this->name, 'upload')
            ));

        }

        $ctype['photos_options'] = $this->options;

        return array($ctype, $items);

    }

}
