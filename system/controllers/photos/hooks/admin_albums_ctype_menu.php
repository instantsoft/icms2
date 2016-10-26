<?php

class onPhotosAdminAlbumsCtypeMenu extends cmsAction {

    public function run($data){

        list($ctype_menu, $ctype) = $data;

        $ctype_menu[] = array(
            'title' => LANG_CP_CONTROLLERS_OPTIONS,
            'url'   => href_to('admin', 'controllers', array('edit', 'photos', 'options'))
        );

        return array($ctype_menu, $ctype);

    }

}
