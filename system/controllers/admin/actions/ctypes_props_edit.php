<?php

class actionAdminCtypesPropsEdit extends cmsAction {

    public function run($ctype_id, $prop_id){

        if (!$ctype_id || !$prop_id) { cmsCore::error404(); }

        $ctype = $this->model_backend_content->getContentType($ctype_id);
        if (!$ctype) { cmsCore::error404(); }

        $form = $this->getForm('ctypes_prop', ['edit', $ctype]);

        $is_submitted = $this->request->has('submit');

        $prop = $this->model_backend_content->getContentProp($ctype['name'], $prop_id);

        if ($is_submitted){

            $prop = $form->parse($this->request, $is_submitted);
            $errors = $form->validate($this,  $prop);

            if (!$errors){

                // если не выбрана группа, обнуляем поле группы
                if (!$prop['fieldset']) { $prop['fieldset'] = null; }

                // если создается новая группа, то выбираем ее
                if ($prop['new_fieldset']) { $prop['fieldset'] = $prop['new_fieldset']; }
                unset($prop['new_fieldset']);

                // сохраняем поле
                $this->model_backend_content->updateContentProp($ctype['name'], $prop_id, $prop);

                cmsUser::addSessionMessage(LANG_SUCCESS_MSG, 'success');

                $this->redirectToAction('ctypes', ['props', $ctype['id']]);
            }

            if ($errors){
                cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');
            }

        }

        return $this->cms_template->render('ctypes_prop', [
            'do'     => 'edit',
            'ctype'  => $ctype,
            'prop'   => $prop,
            'form'   => $form,
            'errors' => isset($errors) ? $errors : false
        ]);
    }

}
