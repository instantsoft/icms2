<?php

class onAdminMenuAdmin extends cmsAction {

    public function run($item) {

        $action       = $item['action'];
        $menu_item_id = $item['menu_item_id'];

        $result = ['url' => href_to($this->name), 'items' => []];

        if ($action != 'menu') {
            return $result;
        }

        $menu = $this->getAdminMenu();

        foreach ($menu as $id => $_item) {

            if ($_item['url'] == href_to($this->name)) {
                continue;
            }

            $result['items'][] = [
                'id'           => 'admin' . $id,
                'parent_id'    => $menu_item_id,
                'title'        => $_item['title'],
                'options'      => !empty($_item['options']) ? $_item['options'] : [],
                'childs_count' => 0,
                'url'          => $_item['url']
            ];
        }

        return $result;
    }

}
