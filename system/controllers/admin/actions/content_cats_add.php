<?php

class actionAdminContentCatsAdd extends cmsAction {

    public function run($ctype_id = false, $parent_id = 1){

        if (!$ctype_id) { return cmsCore::error404(); }

        $content_model = cmsCore::getModel('content');

        $ctype = $content_model->getContentType($ctype_id);
        if (!$ctype) { return cmsCore::error404(); }

        $form = $this->getForm('content_category');

        if (!$content_model->isContentPropsExists($ctype['name'])){
            $form->removeField('basic', 'is_inherit_binds');
        }

        // Форма отправлена?
        $is_submitted = $this->request->has('submit');

        // Парсим форму и получаем поля записи
        $category = $form->parse($this->request, $is_submitted);

        $back_url = $this->request->get('back', '');

        if (!$is_submitted && $parent_id) { $category['parent_id'] = $parent_id; }

        if ($is_submitted){

            // Проверям правильность заполнения
            $errors = $form->validate($this,  $category);

            if (!$errors){

                $this->createCategories($content_model, $ctype, $category);

                if ($back_url){
                    $this->redirect($back_url);
                } else {
                    $this->redirectToAction('content');
                }

            }

            if ($errors){
                cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');
            }

        }

        if (!$ctype['is_cats']){
            cmsUser::addSessionMessage(sprintf(LANG_CP_CTYPE_CATEGORIES_OFF, href_to($this->name, 'ctypes', array('edit', $ctype['id'])).'#tab-categories'));
        }

        return $this->cms_template->render('content_cats_add', array(
            'ctype'    => $ctype,
            'category' => $category,
            'form'     => $form,
            'back_url' => $back_url,
            'errors'   => isset($errors) ? $errors : false
        ));

    }

    private function createCategories($content_model, $ctype, $data){

        $list = explode("\n", $data['title']);

        $levels_ids          = array();
        $cats_ids            = array();
        $is_first            = true;
        $remove_level_offset = 0;

        $parent_props = $content_model->getContentPropsBinds($ctype['name'], $data['parent_id']);
        $parent_props = array_collection_to_list($parent_props, 'id', 'prop_id');

        foreach($list as $category_title){

            $category_title = trim($category_title);

            if (!$category_title) { continue; }

			$level = mb_strlen(str_replace(' ', '', $category_title)) - mb_strlen(ltrim(str_replace(' ', '', $category_title), '- '));

			if ($is_first && $level > 0){ $remove_level_offset = $level; }

			$level = $level - $remove_level_offset;

            $is_sub = $level>0;

			$is_first = false;

			$category_title = ltrim($category_title, '- ');

            if (!$is_sub){

                $levels_ids = array();

                $result = $content_model->addCategory($ctype['name'], array(
                    'parent_id' => $data['parent_id'],
                    'title' => $category_title
                ));

                $levels_ids[0] = $result['id'];
                $cats_ids[] = $result['id'];

                continue;

            }

            $parent_id = $levels_ids[$level - 1];

            if (!$category_title) { $category_title = 'untitled'; }

            $result = $content_model->addCategory($ctype['name'], array(
                'parent_id' => $parent_id,
                'title' => ltrim($category_title, '- ')
            ));

            $levels_ids[$level] = $result['id'];
            $cats_ids[] = $result['id'];

        }

        if (!empty($data['is_inherit_binds']) && $parent_props && $cats_ids){
            foreach($parent_props as $prop_id){
                $content_model->bindContentProp($ctype['name'], $prop_id, $cats_ids);
            }
        }

    }

}
