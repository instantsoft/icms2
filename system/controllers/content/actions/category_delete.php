<?php

class actionContentCategoryDelete extends cmsAction {

    public function run(){

        // Получаем название типа контента и сам тип
        $ctype = $this->model->getContentTypeByName($this->request->get('ctype_name', ''));
        if (!$ctype) { cmsCore::error404(); }

        // проверяем наличие доступа
        if (!cmsUser::isAllowed($ctype['name'], 'delete_cat')) { cmsCore::error404(); }

        $category = $this->model->getCategory($ctype['name'], $this->request->get('id', 0));
        if (!$category) { cmsCore::error404(); }

        if ($category['path'] && sizeof($category['path']) > 1){
            $path   = array_values($category['path']);
            $parent = $path[sizeof($category['path']) - 2];
        }

        $this->model->deleteCategory($ctype['name'], $category['id'], true);

        $back_url = $this->request->get('back', '');

        if ($back_url){
            $this->redirect($back_url);
        } else {
            if ($ctype['options']['list_on']){
                if (isset($parent)){
                    $this->redirectTo($ctype['name'], $parent['slug']);
                } else {
                    $this->redirectTo($ctype['name']);
                }
            } else {
                $this->redirectToHome();
            }
        }

    }

}