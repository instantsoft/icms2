<?php

class actionAdminCtypesFieldsAdd extends cmsAction {

    public function run($ctype_id){

        if (!$ctype_id) { cmsCore::error404(); }

        $ctype = $this->model_content->getContentType($ctype_id);
        if (!$ctype) { cmsCore::error404(); }

        $form = $this->getForm('ctypes_field', array('add', $ctype['name']));

        $form = cmsEventsManager::hook('ctype_field_form', $form);
        list($form, $ctype) = cmsEventsManager::hook($ctype['name'].'_ctype_field_form', array($form, $ctype));

        $field = array('ctype_id' => $ctype['id']);

        if ($this->request->has('submit')){

            // добавляем поля настроек типа поля в общую форму
            // чтобы они были обработаны парсером и валидатором
            // вместе с остальными полями
            $field_type    = $this->request->get('type', '');
            $field_class   = "field" . string_to_camel('_', $field_type);
            $field_object  = new $field_class(null, null);
            $field_options = $field_object->getOptions();

            foreach ($field_options as $option_field) {
                $option_field->setName("options:{$option_field->name}");
                $form->addField('type', $option_field);
            }

            $field = $form->parse($this->request, true);

            $errors = $form->validate($this,  $field);

            $field['ctype_id'] = $ctype['id'];

            if (!$errors){

                // если не выбрана группа, обнуляем поле группы
                if (!$field['fieldset']) { $field['fieldset'] = null; }

                // если создается новая группа, то выбираем ее
                if ($field['new_fieldset']) { $field['fieldset'] = $field['new_fieldset']; }
                unset($field['new_fieldset']);

                // сохраняем поле
                $field_id = $this->model_content->addContentField($ctype['name'], $field);

                if ($field_id){ cmsUser::addSessionMessage(sprintf(LANG_CP_FIELD_CREATED, $field['title']), 'success'); }

                $this->redirectToAction('ctypes', array('fields', $ctype['id']));

            }

            if ($errors){

                cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');

            }

        }

        return $this->cms_template->render('ctypes_field', array(
            'do'     => 'add',
            'ctype'  => $ctype,
            'field'  => $field,
            'form'   => $form,
            'errors' => isset($errors) ? $errors : false
        ));

    }

}
