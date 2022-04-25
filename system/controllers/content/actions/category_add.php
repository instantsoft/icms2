<?php
/**
 * @property \modelContent $model
 */
class actionContentCategoryAdd extends cmsAction {

    public function run() {

        // Получаем название типа контента и сам тип
        $ctype_name = $this->request->get('ctype_name', '');

        $ctype = $this->model->getContentTypeByName($ctype_name);
        if (!$ctype) {
            return cmsCore::error404();
        }

        // проверяем поддержку категорий
        if (!$ctype['is_cats']) {
            return cmsCore::error404();
        }

        // проверяем наличие доступа
        if (!cmsUser::isAllowed($ctype['name'], 'add_cat')) {
            return cmsCore::error404();
        }

        $parent_id = $this->request->get('to_id', 0);

        $form = $this->getCategoryForm($ctype, 'add');

        // Форма отправлена?
        $is_submitted = $this->request->has('submit');

        $category = [];

        list($ctype, $form, $category) = cmsEventsManager::hook('content_cat_add_form', [$ctype, $form, $category], null, $this->request);
        list($form, $category) = cmsEventsManager::hook("content_{$ctype['name']}_cat_form", [$form, $category]);

        // Парсим форму и получаем поля записи
        $category = $form->parse($this->request, $is_submitted);

        if (!$is_submitted && $parent_id) {
            $category['parent_id'] = $parent_id;
        }

        if ($is_submitted) {

            // Проверям правильность заполнения
            $errors = $form->validate($this, $category);

            // Добавляем запись и редиректим на ее просмотр
            if (!$errors) {

                $category = $this->model->addCategory($ctype['name'], $category, !empty($ctype['options']['is_cats_first_level_slug']));

                list($ctype, $category) = cmsEventsManager::hook('content_category_after_add', [
                    $ctype, $category
                ]);

                list($ctype, $category) = cmsEventsManager::hook('content_'.$ctype['name'].'_category_after_add', [
                    $ctype, $category
                ]);

                $this->redirectTo($ctype['name'], $category['slug']);
            }

            if ($errors) {
                cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');
            }
        }

        return $this->cms_template->render('category_form', [
            'do'       => 'add',
            'ctype'    => $ctype,
            'category' => $category,
            'form'     => $form,
            'back_url' => false,
            'errors'   => isset($errors) ? $errors : false
        ]);
    }

}
