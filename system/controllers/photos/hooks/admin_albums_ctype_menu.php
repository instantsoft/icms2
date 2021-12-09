<?php

class onPhotosAdminAlbumsCtypeMenu extends cmsAction {

    public function run($data) {

        list($ctype_menu, $ctype) = $data;

        $menu_item = [
            'title'   => LANG_CP_CONTROLLERS_OPTIONS,
            'url'     => href_to('admin', 'controllers', ['edit', 'photos', 'options']),
            'options' => [
                'icon' => 'cog'
            ]
        ];

        $this->cms_template->addMenuItem('breadcrumb-menu', $menu_item);

        // совместимость со старой админкой
        if ($this->cms_template->name === 'default') {
            $ctype_menu[] = $menu_item;
        }

        return [$ctype_menu, $ctype];
    }

}
