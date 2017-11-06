<?php

class actionAdminMenuTreeAjax extends cmsAction {

    public function run(){

        if (!$this->request->isAjax()) { cmsCore::error404(); }

        $id = $this->request->get('id', '');

        if (!$id || !preg_match('/^([0-9\.]+)$/i', $id)){ cmsCore::error404(); }

        $menu_model = cmsCore::getModel('menu');

        list ($menu_id, $parent_id) = explode('.', $id);

        $items = $menu_model->getMenuItems($menu_id, $parent_id);

        $tree_nodes = array();

        if ($items){
            foreach($items as $item){
                $tree_nodes[] = array(
                    'title'  => $item['title'],
                    'key'    => "{$menu_id}.{$item['id']}",
                    'isLazy' => ($item['childs_count'] > 0)
                );
            }
        }

        $this->cms_template->renderJSON($tree_nodes);

    }

}
