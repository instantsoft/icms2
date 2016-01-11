<?php

class onContentMenuContent extends cmsAction {

    public function run($item){

        $action       = $item['action'];
        $menu_item_id = $item['menu_item_id'];

        if ($action == 'add'){

            return $this->getMenuAddItems($menu_item_id);

        } elseif($action == 'private_list') {

            if(!cmsUser::isLogged()){
                return false;
            }

            return $this->getMenuPrivateItems($menu_item_id);

        } else {

            $ctype = $this->model->getContentTypeByName($action);
            if (!$ctype) { return false; }

            return $this->getMenuCategoriesItems($menu_item_id, $ctype);

        }

    }

}