<?php

class actionAdminCtypesPropsEdit extends cmsAction {

    public function run($ctype_id, $prop_id){

        if (!$ctype_id || !$prop_id) { cmsCore::error404(); }

        $content_model = cmsCore::getModel('content');

        $ctype = $content_model->getContentType($ctype_id);
        if (!$ctype) { cmsCore::error404(); }

        $form = $this->getForm('ctypes_prop', array('edit', $ctype['name']));

        $is_submitted = $this->request->has('submit');

        $prop = $content_model->getContentProp($ctype['name'], $prop_id);

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
                $content_model->updateContentProp($ctype['name'], $prop_id, $prop);

                cmsUser::addSessionMessage(LANG_SUCCESS_MSG, 'success');

                $this->redirectToAction('ctypes', array('props', $ctype['id']));

            }

            if ($errors){
                cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');
            }

        }

        return $this->cms_template->render('ctypes_prop', array(
            'do'     => 'edit',
            'ctype'  => $ctype,
            'prop'   => $prop,
            'form'   => $form,
            'errors' => isset($errors) ? $errors : false
        ));

    }

}
