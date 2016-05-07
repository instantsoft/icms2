<?php
class widgetContentCategories extends cmsWidget {

    public $is_cacheable = false;

    public function run(){

        $ctype_name = $this->getOption('ctype_name');

        $active_cat = false;

        if (!$ctype_name){

            $ctype = cmsModel::getCachedResult('current_ctype');
            if(!$ctype){ return false; }

            $ctype_name = $ctype['name'];

            if(strpos(cmsCore::getInstance()->uri, '.html') === false){

                $current_ctype_category = cmsModel::getCachedResult('current_ctype_category');
                if(!empty($current_ctype_category['id'])){
                    $active_cat = $current_ctype_category;
                }

            } else {

                $item = cmsModel::getCachedResult('current_ctype_item');
                if(!$item){ return false; }

                if(!empty($item['category'])){
                    $active_cat = $item['category'];
                }

            }

        }

        $model = cmsCore::getModel('content');

        $cats = $model->getCategoriesTree($ctype_name, $this->getOption('is_root'));
        if (!$cats) { return false; }

        if(!$active_cat){
            $active_cat = reset($cats);
        }

        $path = $model->getCategoryPath($ctype_name, $active_cat);

        return array(
            'ctype_name' => $ctype_name,
            'cats'       => $cats,
            'active_cat' => $active_cat,
            'path'       => $path
        );

    }

}
