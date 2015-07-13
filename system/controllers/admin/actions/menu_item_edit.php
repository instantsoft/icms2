<?php

class actionAdminMenuItemEdit extends cmsAction {

    public function run($id){

        if (!$id) { cmsCore::error404(); }

        $menu_model = cmsCore::getModel('menu');

        $form = $this->getForm('menu_item');

        $is_submitted = $this->request->has('submit');

        $item = $menu_model->getMenuItem($id);

        if ($is_submitted){

            $item = $form->parse($this->request, $is_submitted);
            $errors = $form->validate($this, $item);

            if (!$errors){

                $menu_model->updateMenuItem($id, $item);

                $this->redirectToAction('menu');

            }

            if ($errors){

                cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');

            }

        }

        return cmsTemplate::getInstance()->render('menu_item', array(
            'do' => 'edit',
            'item' => $item,
            'form' => $form,
            'errors' => isset($errors) ? $errors : false
        ));

    }

}
