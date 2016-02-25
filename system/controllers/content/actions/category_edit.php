<?php

class actionContentCategoryEdit extends cmsAction {

    public function run(){

        // Получаем название типа контента и сам тип
        $ctype_name = $this->request->get('ctype_name', '');
        $ctype = $this->model->getContentTypeByName($ctype_name);
        if (!$ctype) { cmsCore::error404(); }

        // проверяем поддержку категорий
        if (!$ctype['is_cats']){ cmsCore::error404(); }

        // проверяем наличие доступа
        if (!cmsUser::isAllowed($ctype['name'], 'edit_cat')) { cmsCore::error404(); }

        $id = $this->request->get('id', 0);
        if (!$id) { cmsCore::error404(); }

        $category = $this->model->getCategory($ctype['name'], $id);

        $form = $this->getCategoryForm($ctype, 'edit');

        list($form, $category) = cmsEventsManager::hook("content_{$ctype['name']}_cat_form", array($form, $category));

        $back_url = $this->request->get('back', '');

        // Форма отправлена?
        $is_submitted = $this->request->has('submit');

        if ($is_submitted){

            // Парсим форму и получаем поля записи
            $category = $form->parse($this->request, $is_submitted);

            // Проверям правильность заполнения
            $errors = $form->validate($this,  $category);

            if (!$errors){

                // Обнуляем ручной SLUG, ключевые слова и описание
                // если они не разрешены для ручного ввода
                if ($ctype['options']['is_cats_auto_url']){ $category['slug_key'] = null; }
                if (!$ctype['options']['is_cats_keys']){ $category['seo_keys'] = null; }
                if (!$ctype['options']['is_cats_desc']){ $category['seo_desc'] = null; }

                // Добавляем категорию и редиректим на ее просмотр
                $category = $this->model->updateCategory($ctype_name, $id, $category);

                if ($back_url){
                    $this->redirect($back_url);
                } else {
                    if ($ctype['options']['list_on']){
                        $this->redirectTo($ctype_name, $category['slug']);
                    } else {
                        $this->redirectToHome();
                    }
                }

            }

            if ($errors){
                cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');
            }

        }

        // Если включен ручной ввод SLUG и ранее он не был введен, то генерируем
        // его значение по-умолчанию из заголовка
        if (!$ctype['options']['is_cats_auto_url'] && empty($category['slug_key'])){
            $category['slug_key'] = lang_slug($category['title']);
        }

        return cmsTemplate::getInstance()->render('category_form', array(
            'do' => 'edit',
            'ctype' => $ctype,
            'category' => $category,
            'form' => $form,
            'back_url' => $back_url,
            'errors' => isset($errors) ? $errors : false
        ));

    }

}
