<?php
/**
 * @property \modelContent $model
 */
class actionContentCategoryEdit extends cmsAction {

    public function run() {

        // Получаем название типа контента и сам тип
        $ctype_name = $this->request->get('ctype_name', '');

        $ctype = $this->model->getContentTypeByName($ctype_name);
        if (!$ctype) {
            return cmsCore::error404();
        }

        // проверяем поддержку категорий
        if (!$ctype['is_cats'] && !$this->cms_user->is_admin) {
            return cmsCore::error404();
        }

        // проверяем наличие доступа
        if (!cmsUser::isAllowed($ctype['name'], 'edit_cat')) {
            return cmsCore::error404();
        }

        $id = $this->request->get('id', 0);
        if (!$id) {
            return cmsCore::error404();
        }

        $category = $this->model->localizedOff()->getCategory($ctype['name'], $id);
        if (!$category) {
            return cmsCore::error404();
        }

        $this->model->localizedRestore();

        $form = $this->getCategoryForm($ctype, 'edit');

        list($ctype, $form, $category) = cmsEventsManager::hook('content_cat_edit_form', [$ctype, $form, $category], null, $this->request);
        list($form, $category) = cmsEventsManager::hook("content_{$ctype['name']}_cat_form", [$form, $category]);

        $back_url = $this->getRequestBackUrl();

        if ($this->request->has('submit')) {

            // Парсим форму и получаем поля записи
            $new_category = $form->parse($this->request, true);

            // Проверям правильность заполнения
            $errors = $form->validate($this, $new_category);

            if (!$errors) {

                // Обнуляем ручной SLUG, ключевые слова и описание
                // если они не разрешены для ручного ввода
                if ($ctype['options']['is_cats_auto_url']) {
                    $new_category['slug_key'] = null;
                }
                if (!$ctype['options']['is_cats_keys']) {
                    $new_category['seo_keys'] = null;
                }
                if (!$ctype['options']['is_cats_desc']) {
                    $new_category['seo_desc'] = null;
                }

                // Добавляем категорию и редиректим на ее просмотр
                $new_category = $this->model->updateCategory($ctype_name, $id, $new_category, !empty($ctype['options']['is_cats_first_level_slug']));

                list($ctype, $category, $new_category) = cmsEventsManager::hook('content_category_after_update', [
                    $ctype, $category, $new_category
                ]);

                list($ctype, $category, $new_category) = cmsEventsManager::hook('content_' . $ctype['name'] . '_category_after_update', [
                    $ctype, $category, $new_category
                ]);

                if ($back_url) {
                    $this->redirect($back_url);
                } else {
                    if ($ctype['options']['list_on']) {
                        $this->redirectTo($ctype_name, $new_category['slug']);
                    } else {
                        $this->redirectToHome();
                    }
                }
            }

            $category = $new_category;

            if ($errors) {
                cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');
            }
        }

        // Если включен ручной ввод SLUG и ранее он не был введен, то генерируем
        // его значение по умолчанию из заголовка
        if (!$ctype['options']['is_cats_auto_url'] && empty($category['slug_key'])) {
            $category['slug_key'] = lang_slug($category['title']);
        }

        return $this->cms_template->render('category_form', [
            'do'       => 'edit',
            'ctype'    => $ctype,
            'category' => $category,
            'form'     => $form,
            'back_url' => $back_url,
            'errors'   => isset($errors) ? $errors : false
        ]);
    }

}
