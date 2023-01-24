<?php
/**
 * @property \modelMenu $model_menu
 */
class actionAdminMenuItemToggle extends cmsAction {

    public function run($id = false) {

        if (!$id) {

            return $this->cms_template->renderJSON([
                'error' => true
            ]);
        }

        $item = $this->model_menu->getItemByField('menu_items', 'id', $id);
        if (!$item) {

            return $this->cms_template->renderJSON([
                'error' => true
            ]);
        }

        $is_pub = $item['is_enabled'] ? 0 : 1;

        $this->model_menu->update('menu_items', $id, [
            'is_enabled' => $is_pub
        ]);

        cmsCache::getInstance()->clean('menu.items');

        return $this->cms_template->renderJSON([
            'error' => false,
            'is_on' => $is_pub
        ]);
    }

}
