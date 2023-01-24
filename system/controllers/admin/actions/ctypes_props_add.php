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
            'ctype_id' => $ctype_id,
            'cats'     => $cats
        ];

        if ($is_submitted) {

            $prop = $form->parse($this->request, $is_submitted);

            $errors = $form->validate($this, $prop);

            if (!$errors) {

                // если не выбрана группа, обнуляем поле группы
                if (!$prop['fieldset']) {
                    $prop['fieldset'] = null;
                }

                // если создается новая группа, то выбираем ее
                if ($prop['new_fieldset']) {
                    $prop['fieldset'] = $prop['new_fieldset'];
                }
                unset($prop['new_fieldset']);

                $prop['ctype_id'] = $ctype_id;

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
            'errors' => isset($errors) ? $errors : false
        ]);
    }

}
