<?php

class onContentMenuContent extends cmsAction {

    public function run($item){

        $action       = $item['action'];
        $menu_item_id = $item['menu_item_id'];

        if ($action == 'add'){

            return $this->getMenuAddItems($menu_item_id);

        } elseif ($action == 'add_full'){

            return $this->getMenuAddItems($menu_item_id, true);

        } elseif($action == 'private_list') {

            if(!$this->cms_user->is_logged){
                return false;
            }

            return $this->getMenuPrivateItems($menu_item_id);

        } elseif($action == 'trash') {

            if(!$this->cms_user->is_logged){
                return false;
            }

            $ctypes = $this->model->getContentTypes();
            if (!$ctypes) { return false; }

            $allow_restore = false;

            foreach($ctypes as $ctype){
                if (!cmsUser::isAllowed($ctype['name'], 'restore')) { continue; }
                $allow_restore = true; break;
            }

            if(!$allow_restore){ return false; }

            return array(
                'url' => href_to($this->name, 'trash'),
                'items' => false
            );

        } else {

            $ctype = $this->model->getContentTypeByName($action);
            if (!$ctype) { return false; }

            return $this->getMenuCategoriesItems($menu_item_id, $ctype);

        }

    }

}
