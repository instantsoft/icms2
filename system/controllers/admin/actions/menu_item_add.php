<?php

class actionAdminMenuItemAdd extends cmsAction {

    public function run($menu_id = 1, $parent_id = null) {

        $menu = $this->model_menu->getMenu($menu_id);
        if (!$menu) { cmsCore::error404(); }

        $form = $this->getForm('menu_item', [$menu_id, null]);

        $is_submitted = $this->request->has('submit');

        $item = $form->parse($this->request, $is_submitted);

        $item['menu_id'] = $menu_id;
        if (!$this->request->get('parent_id')) { $item['parent_id'] = $parent_id; }

        if ($is_submitted){

            $errors = $form->validate($this, $item);

            if (!$errors){

                $item_id = $this->model_menu->addMenuItem($item);

                if ($item_id){ cmsUser::addSessionMessage(sprintf(LANG_CP_MENU_ITEM_CREATED, $item['title']), 'success'); }

                $this->redirectToAction('menu');

            }

            if ($errors){
                cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');
            }

        }

        return $this->cms_template->render('menu_item', array(
            'do'     => 'add',
            'item'   => $item,
            'menu'   => $menu,
            'form'   => $form,
            'errors' => isset($errors) ? $errors : false
        ));

    }

}
