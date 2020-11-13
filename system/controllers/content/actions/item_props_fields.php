<?php

class actionContentItemPropsFields extends cmsAction {

    public function run() {

        if (!$this->request->isAjax()) {
            return cmsCore::error404();
        }

        $ctype_name  = $this->request->get('ctype_name', '');
        $category_id = $this->request->get('category_id', 0);
        $item_id     = $this->request->get('item_id', 0);
        $add_cats    = $this->request->get('add_cats', []);
        if ($add_cats){
            foreach($add_cats as $index=>$cat_id){
                if (!is_numeric($cat_id) || !$cat_id){
                    unset($add_cats[$index]);
                }
            }
        }

        if (!$ctype_name || (!$category_id && !$add_cats)) {
            return cmsCore::error404();
        }

        $ctype = $this->model->getContentTypeByName($ctype_name);
        if (!$ctype) {
            return cmsCore::error404();
        }

        $add_cats[] = $category_id;

        $values = $item_id ? ['props' => $this->model->getPropsValues($ctype['name'], $item_id)] : [];

        $form = new cmsForm();

        // Добавляется после набора props
        $form->addFieldset('', 'props', array(
            'is_empty' => true,
            'is_hidden' => true
        ));

        $form = $this->addFormPropsFields($form, $ctype, $add_cats);

        // Набор props уже есть в форме, удаляем его тут
        $form->removeFieldset('props');

        ob_start();

        $this->cms_template->renderForm($form, $values, [
            'form_tpl_file' => 'form_fields'
        ]);

        return $this->cms_template->renderJSON(array(
            'success' => true,
            'html'    => ob_get_clean()
        ));
    }

}
