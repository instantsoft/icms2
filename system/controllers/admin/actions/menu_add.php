<?php

class actionAdminMenuAdd extends cmsAction {

    public function run() {

        $menu_model = cmsCore::getModel('menu');

        $form = $this->getForm('menu', array('add'));

        $is_submitted = $this->request->has('submit');

        $menu = $form->parse($this->request, $is_submitted);

        if ($is_submitted) {

            $errors = $form->validate($this, $menu);

            if (!$errors) {

                $menu_id = $menu_model->addMenu($menu);

                if ($menu_id) {
                    cmsUser::addSessionMessage(sprintf(LANG_CP_MENU_CREATED, $menu['title']), 'success');
                }

                cmsUser::setCookiePublic('menu_tree_path', "{$menu_id}.0");

                $this->redirectToAction('menu');
            }

            if ($errors) {
                cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');
            }
        }

        return $this->cms_template->render('menu_form', array(
            'do'     => 'add',
            'item'   => $menu,
            'form'   => $form,
            'errors' => isset($errors) ? $errors : false
        ));
    }

}
