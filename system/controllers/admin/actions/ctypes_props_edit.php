<?php
/**
 * @property \modelBackendContent $model_backend_content
 */
class actionAdminCtypesPropsEdit extends cmsAction {

    public function run($ctype_id = null, $prop_id = null) {

        if (!$ctype_id || !$prop_id) {
            return cmsCore::error404();
        }

        $ctype = $this->model_backend_content->getContentType($ctype_id);

        if (!$ctype) {
            return cmsCore::error404();
        }

        $prop = $this->model_backend_content->localizedOff()->getContentProp($ctype['name'], $prop_id);

        if (!$prop) {
            return cmsCore::error404();
        }

        $this->model_backend_content->localizedRestore();

        $this->dispatchEvent('ctype_loaded', [$ctype, 'props']);

        $form = $this->getForm('ctypes_prop', ['edit', $ctype]);

        $is_submitted = $this->request->has('submit');

        if ($is_submitted) {

            // добавляем поля настроек типа поля в общую форму
            // чтобы они были обработаны парсером и валидатором
            // вместе с остальными полями
            $field_type   = $this->request->get('type', '');
            $field_class  = 'field' . string_to_camel('_', $field_type);

            if (!class_exists($field_class)) {
                return cmsCore::error(ERR_CLASS_NOT_FOUND);
            }

            $field_object = new $field_class(null, [
                'subject_name' => $ctype['name']
            ]);

            $field_options = $field_object->getOptions();

            $form->addFieldsetAfter('type', LANG_CP_FIELD_TYPE_OPTS, 'field_settings');

            $form->mergeForm($this->makeForm(function ($form) use ($field_options) {

                $form->addFieldset(LANG_CP_FIELD_TYPE_OPTS, 'field_settings');

                foreach ($field_options as $field_field) {

                    $field_field->setName("options:{$field_field->name}");

                    $form->addField('field_settings', $field_field);
                }

                return $form;
            }));

            $prop   = array_merge($prop, $form->parse($this->request, $is_submitted, $prop));
            $errors = $form->validate($this, $prop);

            if (!$errors) {

                // сохраняем поле
                $this->model_backend_content->updateContentProp($ctype['name'], $prop_id, $prop);

                cmsUser::addSessionMessage(LANG_SUCCESS_MSG, 'success');

                return $this->redirectToAction('ctypes', ['props', $ctype['id']]);
            }

            if ($errors) {
                cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');
            }
        }

        return $this->cms_template->render('ctypes_prop', [
            'do'     => 'edit',
            'ctype'  => $ctype,
            'prop'   => $prop,
            'form'   => $form,
            'errors' => $errors ?? false
        ]);
    }

}
