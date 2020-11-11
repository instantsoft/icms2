<?php

class actionFormsFieldsAdd extends cmsAction {

    public function run($form_id){

        $form_data = $this->model->getForm($form_id);
        if(!$form_data){ cmsCore::error404(); }

        $form = $this->getForm('field', array('add', $form_data['id']));

        $field = ['form_id' => $form_id];

        list($form, $form_data, $field) = cmsEventsManager::hook('forms_field_form', [$form, $form_data, $field]);

        if ($this->request->has('submit')){

            // добавляем поля настроек типа поля в общую форму
            // чтобы они были обработаны парсером и валидатором
            // вместе с остальными полями
            $field_type    = $this->request->get('type', '');
            $field_class   = 'field' . string_to_camel('_', $field_type);
            $field_object  = new $field_class(null, null);
            $field_options = $field_object->getOptions();
            $form->addFieldsetAfter('type', LANG_CP_FIELD_TYPE_OPTS, 'field_settings');
            foreach ($field_options as $option_field) {
                $option_field->setName("options:{$option_field->name}");
                $form->addField('field_settings', $option_field);
            }

            $field = array_merge($field, $form->parse($this->request, true));

            $errors = $form->validate($this,  $field);

            if (!$errors){

                // если не выбрана группа, обнуляем поле группы
                if (!$field['fieldset']) { $field['fieldset'] = null; }

                // если создается новая группа, то выбираем ее
                if ($field['new_fieldset']) { $field['fieldset'] = $field['new_fieldset']; }
                unset($field['new_fieldset']);

                // сохраняем поле
                $field_id = $this->model->addFormField($field);

                if ($field_id){ cmsUser::addSessionMessage(sprintf(LANG_CP_FIELD_CREATED, $field['title']), 'success'); }

                $this->redirectToAction('form_fields', array($form_data['id']));

            }

            if ($errors){
                cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');
            }

        }

        return $this->cms_template->render([
            'menu'      => $this->getFormMenu('edit', $form_data['id']),
            'do'        => 'add',
            'form_data' => $form_data,
            'field'     => $field,
            'form'      => $form,
            'errors'    => isset($errors) ? $errors : false
        ]);

    }

}
