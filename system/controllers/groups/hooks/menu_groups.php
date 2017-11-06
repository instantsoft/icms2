<?php

class onGroupsMenuGroups extends cmsAction {

    public function run($item){

        $action       = $item['action'];
        $menu_item_id = $item['menu_item_id'];

        $result = array('url' => href_to($this->name, 'index', $action), 'items' => false);

        if ($action == 'my'){

            $groups = $this->model->getUserGroups($this->cms_user->id);
            if (!$groups) { return false; }

            foreach($groups as $id => $group){

                $result['items'][] = array(
                    'id'           => 'group' . $id,
                    'parent_id'    => $menu_item_id,
                    'title'        => $group['title'],
                    'childs_count' => 0,
                    'url'          => href_to($this->name, $group['slug'])
                );

            }

        }

        return $result;

    }

}
