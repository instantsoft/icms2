<?php

class actionGroupsFieldsEdit extends cmsAction {

    public function run($field_id){

        if (!$field_id) { cmsCore::error404(); }

        $content_model = cmsCore::getModel('content');

        $content_model->setTablePrefix('');

        $form = $this->getForm('field', array('edit'));
        $form = cmsEventsManager::hook('group_field_form', $form);

        $field = $content_model->getContentField('groups', $field_id);

        // скроем поле "Системное имя" для фиксированных полей
        if ($field['is_fixed']) { $form->hideField('basic', 'name'); }

        // скроем лишние опции для системных полей
        if ($field['is_system']) {
            $form->hideField('type', 'type');
            $form->removeFieldset('format');
            $form->removeFieldset('labels');
            $form->removeFieldset('values');
        }

        // удалим выбор типа для полей с фиксированным типом
        if ($field['is_fixed_type']) {
            $form->hideField('type', 'type');
        }

        if ($this->request->has('submit')){

            // добавляем поля настроек типа поля в общую форму
            // чтобы они были обработаны парсером и валидатором
            // вместе с остальными полями
            $field_type = $this->request->get('type', '');
            $field_class = 'field' . string_to_camel('_', $field_type);
            $field_object = new $field_class(null, null);
            $field_options = $field_object->getOptions();
            foreach($field_options as $option_field){
                $option_field->setName("options:{$option_field->name}");
                $form->addField('type', $option_field);
            }

            $defaults = $field['is_fixed_type'] ? array('type'=>$field['type']) : array();

            $field = array_merge($defaults, $form->parse($this->request, true));
            $errors = $form->validate($this,  $field);

            if (!$errors){

                // если не выбрана группа, обнуляем поле группы
                if (!$field['fieldset']) { $field['fieldset'] = null; }

                // если создается новая группа, то выбираем ее
                if ($field['new_fieldset']) { $field['fieldset'] = $field['new_fieldset']; }
                unset($field['new_fieldset']);

                // сохраняем поле
                $content_model->updateContentField('groups', $field_id, $field);

                cmsUser::addSessionMessage(LANG_CP_SAVE_SUCCESS, 'success');

                $this->redirectToAction('fields');

            }

            if ($errors){
                cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');
            }

        }

        return $this->cms_template->render('backend/field', array(
            'do'     => 'edit',
            'field'  => $field,
            'form'   => $form,
            'errors' => isset($errors) ? $errors : false
        ));

    }

}

