<?php

class actionAdminMenuEdit extends cmsAction {

    public function run($id){

        $menu_model = cmsCore::getModel('menu');

        $form = $this->getForm('menu', array('edit'));

        $is_submitted = $this->request->has('submit');

        $menu = $menu_model->getMenu($id);

        if ($menu['is_fixed']){
            $form->removeField('basic', 'name');
        }

        if ($is_submitted){

            $menu = $form->parse($this->request, $is_submitted);
            $errors = $form->validate($this, $menu);

            if (!$errors){

                $menu_model->updateMenu($id, $menu);

                cmsUser::setCookiePublic('menu_tree_path', "{$id}.0");

                $this->redirectToAction('menu');

            }

            if ($errors){

                cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');

            }

        }

        return cmsTemplate::getInstance()->render('menu_form', array(
            'do' => 'edit',
            'item' => $menu,
            'form' => $form,
            'errors' => isset($errors) ? $errors : false
        ));

    }

}
