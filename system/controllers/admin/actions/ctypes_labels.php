<?php

class actionAdminCtypesLabels extends cmsAction {

    public function run($id = null) {

        if (!$id) {
            return cmsCore::error404();
        }

        $wizard_mode = $this->request->get('wizard_mode', 0);

        $form = $this->getForm('ctypes_labels');

        $ctype = $this->model_backend_content->localizedOff()->getContentType($id);
        if (!$ctype) {
            return cmsCore::error404();
        }

        $this->model_backend_content->localizedRestore();

        $this->dispatchEvent('ctype_loaded', [$ctype, 'edit']);

        cmsCore::loadControllerLanguage('content');

        if ($this->request->has('submit')) {

            $ctype = array_merge($ctype, $form->parse($this->request, true));

            $errors = $form->validate($this, $ctype);

            if (!$errors) {

                $this->model_backend_content->updateContentType($id, $ctype);

                $ctype = cmsEventsManager::hook('ctype_labels_after_update', $ctype);

                if ($wizard_mode) {

                    $this->redirectToAction('ctypes', ['fields', $id], ['wizard_mode' => true]);
                } else {

                    cmsUser::addSessionMessage(LANG_CP_SAVE_SUCCESS, 'success');

                    $this->redirectToAction('ctypes', ['labels', $ctype['id']]);
                }
            }

            if ($errors) {
                cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');
            }
        }

        return $this->cms_template->render('ctypes_labels', [
            'id'     => $id,
            'ctype'  => $ctype,
            'form'   => $form,
            'errors' => isset($errors) ? $errors : false
        ]);
    }

}
