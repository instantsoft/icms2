<?php

class actionAdminMenuItemEdit extends cmsAction {

    public function run($id){

        if (!$id) { cmsCore::error404(); }

        $item = $this->model_menu->getMenuItem($id);
        if (!$item) { cmsCore::error404(); }

        $menu = $this->model_menu->getMenu($item['menu_id']);
        if (!$menu) { cmsCore::error404(); }

        $form = $this->getForm('menu_item');

        if ($this->request->has('submit')){

            $item = $form->parse($this->request, true);
            $errors = $form->validate($this, $item);

            if (!$errors){

                $this->model_menu->updateMenuItem($id, $item);

                cmsUser::addSessionMessage(LANG_CP_SAVE_SUCCESS, 'success');

                $this->redirectToAction('menu');

            }

            if ($errors){
                cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');
            }

        }

        return $this->cms_template->render('menu_item', array(
            'do'     => 'edit',
            'menu'   => $menu,
            'item'   => $item,
            'form'   => $form,
            'errors' => isset($errors) ? $errors : false
        ));

    }

}
