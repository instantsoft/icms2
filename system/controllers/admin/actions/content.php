<?php

class actionAdminContent extends cmsAction {

    public function run($do=false){

        // если нужно, передаем управление другому экшену
        if ($do){
            $this->runAction('content_'.$do, array_slice($this->params, 1));
            return;
        }

        $content_model = cmsCore::getModel('content');

        $ctypes = $content_model->getContentTypes();

        $grid = $this->loadDataGrid('content_items');

        $tree_path = cmsUser::getCookie('content_tree_path');
        if($tree_path && ($tree_path = explode('/', $tree_path)) && !empty($tree_path[1]) && ($ctype_id = (int)$tree_path[1])){
            $ctype = $content_model->getContentType($ctype_id);
            if($ctype){
                $filter_str = cmsUser::getUPS('admin.filter_str.'.$ctype['name']);
                if($filter_str){
                    parse_str($filter_str, $filter);
                    $grid['filter'] = $filter;
                }
            }
        }

        return cmsTemplate::getInstance()->render('content', array(
            'ctypes' => $ctypes,
            'grid' => $grid
        ));

    }

}
