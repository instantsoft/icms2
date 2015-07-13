<?php

class actionAdminMenuItemAdd extends cmsAction {

    public function run($menu_id=1, $parent_id=null){

        $menu_model = cmsCore::getModel('menu');
        $form = $this->getForm('menu_item');

        $is_submitted = $this->request->has('submit');

        $menu = $menu_model->getMenu($menu_id);

        $item = $form->parse($this->request, $is_submitted);

        $item['menu_id'] = $menu_id;
        if (!$this->request->get('parent_id')) { $item['parent_id'] = $parent_id; }

        if ($is_submitted){

            $errors = $form->validate($this, $item);

            if (!$errors){

                $item_id = $menu_model->addMenuItem($item);

                if ($item_id){ cmsUser::addSessionMessage(sprintf(LANG_CP_MENU_ITEM_CREATED, $item['title']), 'success'); }

                $this->redirectToAction('menu');

            }

            if ($errors){

                cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');

            }

        }

        return cmsTemplate::getInstance()->render('menu_item', array(
            'do' => 'add',
            'item' => $item,
            'menu' => $menu,
            'form' => $form,
            'errors' => isset($errors) ? $errors : false
        ));

    }

}
