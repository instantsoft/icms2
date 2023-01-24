<?php
/**
 * @property \modelBackendContent $model_backend_content
 */
class actionAdminCtypesEdit extends cmsAction {

    public function run($id = null) {

        if (!$id) {
            return cmsCore::error404();
        }

        $ctype = $this->model_backend_content->localizedOff()->getContentType($id);
        if (!$ctype) {
            return cmsCore::error404();
        }

        $this->model->localizedRestore();

        $this->dispatchEvent('ctype_loaded', [$ctype, 'edit']);

        $form = $this->getForm('ctypes_basic', ['edit', $ctype]);

        $form = cmsEventsManager::hook('ctype_basic_form', $form);
        $form = cmsEventsManager::hook('ctype_basic_' . $ctype['name'] . '_form', $form);

        $form->hideField('titles', 'name');

        $ctype = cmsEventsManager::hook('ctype_before_edit', $ctype);

        $template = new cmsTemplate($this->cms_config->template);

        // Если есть собственный шаблон для типа контента
        // то удаляем поле выбора стиля
        $tpl_file = $template->getTemplateFileName('content/' . $ctype['name'] . '_list', true);
        if ($tpl_file) {
            $form->removeField('listview', 'options:list_style');
            $form->removeField('listview', 'options:list_style_names');
            $form->removeField('listview', 'options:context_list_style');
        }

        if ($this->request->has('submit')) {

            $ctype  = $form->parse($this->request, true);
            $errors = $form->validate($this, $ctype);

            if (!$errors) {

                $ctype = cmsEventsManager::hook('ctype_before_update', $ctype);
                $ctype = cmsEventsManager::hook("ctype_{$ctype['name']}_before_update", $ctype);

                $this->model_backend_content->updateContentType($id, $ctype);

                $ctype['id'] = $id;

                cmsEventsManager::hook('ctype_after_update', $ctype);
                cmsEventsManager::hook("ctype_{$ctype['name']}_after_update", $ctype);

                cmsUser::addSessionMessage(LANG_CP_SAVE_SUCCESS, 'success');

                $this->redirectToAction('ctypes', ['edit', $ctype['id']]);
            }

            if ($errors) {

                $ctype['id'] = $id;

                cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');
            }
        }

        return $this->cms_template->render('ctypes_basic', [
            'id'     => $id,
            'do'     => 'edit',
            'ctype'  => $ctype,
            'form'   => $form,
            'errors' => isset($errors) ? $errors : false
        ]);
    }

}
