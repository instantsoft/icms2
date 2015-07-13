<?php

class actionAdminCtypesFieldsEdit extends cmsAction {

    public function run($ctype_id, $field_id){

        if (!$ctype_id || !$field_id) { cmsCore::error404(); }

        $content_model = cmsCore::getModel('content');

        $ctype = $content_model->getContentType($ctype_id);
        if (!$ctype) { cmsCore::error404(); }

        $form = $this->getForm('ctypes_field', array('edit', $ctype['name']));

        $is_submitted = $this->request->has('submit');

        $field = $content_model->getContentField($ctype['name'], $field_id);

        // скроем поле "Системное имя" для фиксированных полей
        if ($field['is_fixed']) { $form->hideField('basic', 'name'); }

        // скроем лишние опции для системных полей
        if ($field['is_system']) {
            $form->hideField('basic', 'hint');
            $form->hideFieldset('type');
            $form->hideFieldset('group');
            $form->hideFieldset('format');
            $form->hideFieldset('values');
            $form->hideFieldset('labels');
            $form->hideFieldset('edit_access');
        }

        // удалим выбор типа для полей с фиксированным типом
        if ($field['is_fixed_type']) { $form->removeFieldset('type'); }

        if ($is_submitted){

            // добавляем поля настроек типа поля в общую форму
            // чтобы они были обработаны парсером и валидатором
            // вместе с остальными полями
            if (!$field['is_system'] && !$field['is_fixed_type']){
                $field_type = $this->request->get('type');
                $field_class = "field" . string_to_camel('_', $field_type);
                $field_object = new $field_class(null, null);
                $field_options = $field_object->getOptions();
                foreach($field_options as $option_field){
                    $option_field->setName("options:{$option_field->name}");
                    $form->addField('type', $option_field);
                }
            }

            $defaults = $field['is_fixed_type'] ? array('type'=>$field['type']) : array();

            $field = array_merge($defaults, $form->parse($this->request, $is_submitted));
            $errors = $form->validate($this,  $field);

            if (!$errors){

                // если не выбрана группа, обнуляем поле группы
                if (!$field['fieldset']) { $field['fieldset'] = null; }

                // если создается новая группа, то выбираем ее
                if ($field['new_fieldset']) { $field['fieldset'] = $field['new_fieldset']; }
                unset($field['new_fieldset']);

                // сохраняем поле
                $content_model->updateContentField($ctype['name'], $field_id, $field);

                $this->redirectToAction('ctypes', array('fields', $ctype['id']));

            }

            if ($errors){

                cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');

            }

        }

        return cmsTemplate::getInstance()->render('ctypes_field', array(
            'do' => 'edit',
            'ctype' => $ctype,
            'field' => $field,
            'form' => $form,
            'errors' => isset($errors) ? $errors : false
        ));

    }

}
