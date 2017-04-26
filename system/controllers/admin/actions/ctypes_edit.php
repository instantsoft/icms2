<?php

class actionAdminCtypesEdit extends cmsAction {

    public function run($id){

        if (!$id) { cmsCore::error404(); }

        $content_model = cmsCore::getModel('content');

        $ctype = $content_model->getContentType($id);
        if (!$ctype) { cmsCore::error404(); }

        $form = $this->getForm('ctypes_basic', array('edit'));

        $form = cmsEventsManager::hook('ctype_basic_form', $form);
        $form = cmsEventsManager::hook('ctype_basic_'.$ctype['name'].'_form', $form);

        $form->hideField('titles', 'name');

        $ctype = cmsEventsManager::hook('ctype_before_edit', $ctype);

        // Если есть собственный шаблон для типа контента
        // то удаляем поле выбора стиля
        $tpl_file = $this->cms_template->getTemplateFileName('content/'.$ctype['name'].'_list', true);
        if ($tpl_file) { $form->removeField('listview', 'options:list_style'); }

        if ($this->request->has('submit')){

            $ctype = $form->parse($this->request, true);
            $errors = $form->validate($this,  $ctype);

            if (!$errors){

                $ctype = cmsEventsManager::hook("ctype_before_update", $ctype);
                $ctype = cmsEventsManager::hook("ctype_{$ctype['name']}_before_update", $ctype);

                $content_model->updateContentType($id, $ctype);

                $ctype['id'] = $id;
                cmsEventsManager::hook("ctype_after_update", $ctype);
                cmsEventsManager::hook("ctype_{$ctype['name']}_after_update", $ctype);

                cmsUser::addSessionMessage(LANG_CP_SAVE_SUCCESS, 'success');

                $this->redirectToAction('ctypes', array('edit', $ctype['id']));

            }

            if ($errors){

                cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');

            }

        }

        return $this->cms_template->render('ctypes_basic', array(
            'id'     => $id,
            'do'     => 'edit',
            'ctype'  => $ctype,
            'form'   => $form,
            'errors' => isset($errors) ? $errors : false
        ));

    }

}
