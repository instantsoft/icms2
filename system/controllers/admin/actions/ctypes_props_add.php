<?php
/**
 * @property \modelBackendContent $model_backend_content
 */
class actionAdminCtypesPropsAdd extends cmsAction {

    public function run($ctype_id = null, $category_id = null) {

        if (!$ctype_id || !$category_id) {
            return cmsCore::error404();
        }

        $ctype = $this->model_backend_content->getContentType($ctype_id);
        if (!$ctype) {
            return cmsCore::error404();
        }

        $this->dispatchEvent('ctype_loaded', [$ctype, 'props']);

        $form = $this->getForm('ctypes_prop', ['add', $ctype]);

        $is_submitted = $this->request->has('submit');

        $cats    = [intval($category_id)];
        $subcats = $this->model_backend_content->getSubCategoriesTree($ctype['name'], $category_id, false);

        if (is_array($subcats)) {
            foreach ($subcats as $cat) {
                $cats[] = intval($cat['id']);
            }
        }

        $prop = [
            'cats' => $cats
        ];

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

            $prop = $form->parse($this->request, $is_submitted);

            $errors = $form->validate($this, $prop);

            if (!$errors) {

                // сохраняем поле
                $prop_id = $this->model_backend_content->addContentProp($ctype['name'], $prop);

                if ($prop_id) {
                    cmsUser::addSessionMessage(sprintf(LANG_CP_FIELD_CREATED, $prop['title']), 'success');
                }

                return $this->redirectToAction('ctypes', ['props', $ctype['id']]);
            }

            if ($errors) {
                cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');
            }
        }

        return $this->cms_template->render('ctypes_prop', [
            'do'     => 'add',
            'ctype'  => $ctype,
            'prop'   => $prop,
            'form'   => $form,
            'errors' => $errors ?? false
        ]);
    }

}
