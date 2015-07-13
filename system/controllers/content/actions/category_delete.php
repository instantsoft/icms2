<?php

class actionContentCategoryDelete extends cmsAction {

    public function run(){

        // Получаем название типа контента и сам тип
        $ctype_name = $this->request->get('ctype_name');
        $ctype = $this->model->getContentTypeByName($ctype_name);
        if (!$ctype) { cmsCore::error404(); }

        // проверяем наличие доступа
        if (!cmsUser::isAllowed($ctype['name'], 'delete_cat')) { cmsCore::error404(); }

        $id = $this->request->get('id');
        if (!$id) { cmsCore::error404(); }

        $category = $this->model->getCategory($ctype_name, $id);

        if (sizeof($category['path']>1)){
            $path = array_values($category['path']);
            $parent = $path[ sizeof($category['path']) - 2 ];
        }

        $this->model->deleteCategory($ctype_name, $id, true);

        $back_url = $this->request->get('back');

        if ($back_url){
            $this->redirect($back_url);
        } else {
            if ($ctype['options']['list_on']){
                if (isset($parent)){
                    $this->redirectTo($ctype_name, $parent['slug']);
                } else {
                    $this->redirectTo($ctype_name);
                }
            } else {
                $this->redirectToHome();
            }
        }

    }

}
