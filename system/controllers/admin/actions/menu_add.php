<?php
/**
 * @property \modelMenu $model_menu
 */
class actionAdminMenuAdd extends cmsAction {

    public function run() {

        $form = $this->getForm('menu', ['add']);

        $is_submitted = $this->request->has('submit');

        $menu = $form->parse($this->request, $is_submitted);

        if ($is_submitted) {

            $errors = $form->validate($this, $menu);

            if (!$errors) {

                $menu_id = $this->model_menu->addMenu($menu);

                if ($menu_id) {
                    cmsUser::addSessionMessage(sprintf(LANG_CP_MENU_CREATED, $menu['title']), 'success');
                }

                cmsUser::setCookiePublic('menu_tree_path', "{$menu_id}.0");

                return $this->redirectToAction('menu');
            }

            if ($errors) {
                cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');
            }
        }

        return $this->cms_template->render('menu_form', [
            'do'     => 'add',
            'item'   => $menu,
            'form'   => $form,
            'errors' => $errors ?? false
        ]);
    }

}
