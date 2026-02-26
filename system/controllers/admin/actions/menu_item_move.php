<?php
/**
 * @property \modelMenu $model_menu
 */
class actionAdminMenuItemMove extends cmsAction {

    public function run() {

        if (!$this->request->isAjax()) {
            return cmsCore::error404();
        }

        $items = $this->request->get('selected', []);

        $is_submitted = $this->request->has('items');

        $form = $this->getForm('menu_item_move');

        $data = $form->parse($this->request, $is_submitted);

        if ($is_submitted) {

            $errors = $form->validate($this, $data);

            if (!$errors) {

                $data['items'] = explode(',', $data['items']);

                $this->model_menu->moveMenuItem($data['menu_id'], $data['items']);

                return $this->cms_template->renderJSON([
                    'errors' => false,
                    'redirect_uri' => $this->cms_template->href_to('menu')
                ]);
            }

            return $this->cms_template->renderJSON([
                'errors' => $errors
            ]);
        }

        if (!$items || empty($items[0]) || !is_numeric($items[0])) {
            return cmsCore::error404();
        }

        $menu_item_id = $items[0];

        $item = $this->model_menu->getMenuItem($menu_item_id);
        if (!$item) {
            return cmsCore::error404();
        }

        return $this->cms_template->render('menu_item_move', [
            'items'     => $items,
            'menu_id'   => $item['menu_id'],
            'form'      => $form,
            'errors'    => $errors ?? false
        ]);
    }

}
