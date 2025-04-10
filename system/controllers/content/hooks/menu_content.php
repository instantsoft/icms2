<?php

class onContentMenuContent extends cmsAction {

    public function run($item) {

        switch ($item['action']) {
            case 'add':

                return $this->getMenuAddItems($item['menu_item_id']);

            case 'add_full':

                return $this->getMenuAddItems($item['menu_item_id'], true);

            case 'private_list':

                if (!$this->cms_user->is_logged) {
                    return false;
                }

                return $this->getMenuPrivateItems($item['menu_item_id']);

            case 'trash':

                if (!$this->cms_user->is_logged) {
                    return false;
                }

                $ctypes = $this->model->getContentTypes();
                if (!$ctypes) {
                    return false;
                }

                $allow_restore = false;

                foreach ($ctypes as $ctype) {
                    if (cmsUser::isAllowed($ctype['name'], 'restore')) {
                        $allow_restore = true;
                        break;
                    }
                }

                if (!$allow_restore) {
                    return false;
                }

                return [
                    'url'   => href_to($this->name, 'trash'),
                    'items' => false
                ];

            default:
                break;
        }


        $ctype = $this->model->getContentTypeByName($item['action']);
        if (!$ctype) {
            return false;
        }

        return $this->getMenuCategoriesItems($item['menu_item_id'], $ctype);
    }

}
