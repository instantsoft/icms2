<?php

class actionAdminContent extends cmsAction {

    public function run($do = false) {

        $ctype_id = 0; $ctype = [];

        // если нужно, передаем управление другому экшену
        if ($do){
            if(!is_numeric($do)){
                $this->runExternalAction('content_'.$do, array_slice($this->params, 1));
                return;
            } else {
                $ctype_id = $do;
            }
        }

        $content_model = cmsCore::getModel('content');

        $ctypes = $content_model->getContentTypes();

        $key_path = '/1.1';

        // Сохранённый путь дерева
        $tree_path = cmsUser::getCookie('content_tree_path');

        if($tree_path){
            $tree_path = explode('/', trim($tree_path, '/'));
        }

        // Если $ctype_id передан, формируем $key_path
        if($ctype_id){

            $key_path = '/'.$ctype_id.'.1';

            // дополняем его категориями, если в куках этот тип контента
            if($tree_path && '/'.$tree_path[0] === $key_path){
                $key_path = '/'.implode('/', $tree_path);
            }

        } else {

            // Иначе, берём id типа контента из кук и формируем $key_path
            if($tree_path){

                $ctype_id = (int)$tree_path[0];

                $key_path = '/'.implode('/', $tree_path);

            }

        }

        if($ctype_id){

            $ctype = $content_model->getContentType($ctype_id);

            if(!$ctype){
                return cmsCore::error404();
            }

        }

        if(!empty($ctype)){
            $grid = $this->loadDataGrid('content_items', $ctype['name'], 'admin.grid_filter.content.'.$ctype['name']);
        } else {
            $grid = $this->loadDataGrid('content_items');
        }

        $diff_order = cmsUser::getUPS('admin.grid_filter.content.diff_order');

        return $this->cms_template->render('content', array(
            'key_path'   => $key_path,
            'ctype'      => $ctype,
            'ctype_id'   => $ctype_id,
            'ctypes'     => $ctypes,
            'grid'       => $grid,
            'diff_order' => $diff_order
        ));

    }

}
