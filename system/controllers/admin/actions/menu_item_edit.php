<?php
/**
 * @property \modelMenu $model_menu
 */
class actionAdminMenuItemEdit extends cmsAction {

    public function run($id = null) {

        if (!$id) {
            return cmsCore::error404();
        }

        $item = $this->model_menu->localizedOff()->getMenuItem($id);
        if (!$item) {
            return cmsCore::error404();
        }

        $this->model_menu->localizedRestore();

        $menu = $this->model_menu->getMenu($item['menu_id']);
        if (!$menu) {
            return cmsCore::error404();
        }

        $form = $this->getForm('menu_item', [$item['menu_id'], $item['id']]);

        if ($this->request->has('submit')) {

            $item   = $form->parse($this->request, true);
            $errors = $form->validate($this, $item);

            if (!$errors) {

                $this->model_menu->updateMenuItem($id, $item);

                cmsUser::addSessionMessage(LANG_CP_SAVE_SUCCESS, 'success');

                $this->redirectToAction('menu');
            }

            if ($errors) {
                cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');
            }
        }

        return $this->cms_template->render('menu_item', [
            'do'     => 'edit',
            'menu'   => $menu,
            'item'   => $item,
            'form'   => $form,
            'errors' => isset($errors) ? $errors : false
        ]);
    }

}
