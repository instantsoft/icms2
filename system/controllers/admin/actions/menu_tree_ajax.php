<?php
/**
 * @property \modelMenu $model_menu
 */
class actionAdminMenuTreeAjax extends cmsAction {

    public function run() {

        if (!$this->request->isAjax()) {
            return cmsCore::error404();
        }

        $id = $this->request->get('id', '');

        if (!$id || !preg_match('/^([0-9\.]+)$/i', $id)) {
            return cmsCore::error404();
        }

        list ($menu_id, $parent_id) = explode('.', $id);

        $items = $this->model_menu->getMenuItems($menu_id, $parent_id);

        $tree_nodes = [];

        if ($items) {
            foreach ($items as $item) {
                $tree_nodes[] = [
                    'title'  => html($item['title'], false),
                    'key'    => "{$menu_id}.{$item['id']}",
                    'isLazy' => ($item['childs_count'] > 0)
                ];
            }
        }

        return $this->cms_template->renderJSON($tree_nodes);
    }

}
