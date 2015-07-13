<?php

class actionAdminCtypesEdit extends cmsAction {

    public function run($id){

        if (!$id) { cmsCore::error404(); }

        $content_model = cmsCore::getModel('content');

        $form = $this->getForm('ctypes_basic', array('edit'));

        $form = cmsEventsManager::hook("ctype_basic_form", $form);

        $form->hideField('titles', 'name');

        $is_submitted = $this->request->has('submit');

        $ctype = $content_model->getContentType($id);

        if (!$ctype) { cmsCore::error404(); }

        $ctype = cmsEventsManager::hook("ctype_before_edit", $ctype);

        $template = cmsTemplate::getInstance();

        // Если есть собственный шаблон для типа контента
        // то удаляем поле выбора стиля
        $tpl_file = $template->getTemplateFileName('content/'.$ctype['name'].'_list', true);
        if ($tpl_file) { $form->removeField('listview', 'options:list_style'); }

        if ($is_submitted){

            $ctype = $form->parse($this->request, $is_submitted);
            $errors = $form->validate($this,  $ctype);

            if (!$errors){

                $ctype = cmsEventsManager::hook("ctype_before_update", $ctype);
                $ctype = cmsEventsManager::hook("ctype_{$ctype['name']}_before_update", $ctype);

                $content_model->updateContentType($id, $ctype);

                $ctype['id'] = $id;
                cmsEventsManager::hook("ctype_after_update", $ctype);
                cmsEventsManager::hook("ctype_{$ctype['name']}_after_update", $ctype);

                $this->redirectToAction('ctypes');

            }

            if ($errors){

                cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');

            }

        }

        return $template->render('ctypes_basic', array(
            'id' => $id,
            'do' => 'edit',
            'ctype' => $ctype,
            'form' => $form,
            'errors' => isset($errors) ? $errors : false
        ));

    }

}
