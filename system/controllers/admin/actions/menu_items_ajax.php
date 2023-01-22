<?php
/**
 * @property \modelMenu $model_menu
 */
class actionAdminMenuItemsAjax extends cmsAction {

    public function run($menu_id, $parent_id) {

        if (!$this->request->isAjax()) {
            return cmsCore::error404();
        }

        $grid = $this->loadDataGrid('menu_items');

        $items = $this->model_menu->localizedOn()->getMenuItems($menu_id, $parent_id);

        $total = $items ? 1 : 0;

        $this->cms_template->renderGridRowsJSON($grid, $items, $total);

        return $this->halt();
    }

}
