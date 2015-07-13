<?php
class widgetContentCategories extends cmsWidget {

    public $is_cacheable = false;

    public function run(){

        $ctype_name = $this->getOption('ctype_name');

        $slug = null;

        if (!$ctype_name){

            $core = cmsCore::getInstance();

            if ($core->controller != 'content'){ return false; }

			$uri_segs = explode('/', $core->uri);
			
            $ctype_string = $uri_segs[0];
            $slug = !mb_strstr($core->uri, '.html') ? mb_substr($core->uri, mb_strlen($ctype_string)+1) : null;

			$matches = array();
			
            if (preg_match('/^([a-z0-9]+)$/', $ctype_string, $matches)){
                $ctype_name = $matches[0];
            } else
            if (preg_match('/^([a-z0-9]+)-([a-z0-9_]+)$/', $ctype_string, $matches)){
                $ctype_name = $matches[1];
            } else {
                return false;
            }

        }

        $model = cmsCore::getModel('content');

        $cats = $model->getCategoriesTree($ctype_name, $this->getOption('is_root'));

        if (!$cats) { return false; }

        $active_cat = array('id'=>0);

        foreach($cats as $id=>$cat){
            if (($cat['slug'] === $slug) || (!$cat['slug'] && !$slug)){
                $active_cat = $cat;
                break;
            }
        }

        $path = $model->getCategoryPath($ctype_name, $active_cat);

        return array(
            'ctype_name' => $ctype_name,
            'cats' => $cats,
            'active_cat' => $active_cat,
            'path' => $path,
            'slug' => $slug,
        );

    }

}
