<?php

class widgetContentCategories extends cmsWidget {

    public $is_cacheable = false;

    public function run() {

        $ctype = cmsModel::getCachedResult('current_ctype');

        $ctype_name = $this->getOption('ctype_name');

        $active_cat = false;

        if (!$ctype_name) {

            if (!$ctype) { return false; }

            $ctype_name = $ctype['name'];

        }

        if ($ctype && $ctype['name'] == $ctype_name) {

            if (!$ctype['is_cats']) { return false; }

            if (strpos(cmsCore::getInstance()->uri, '.html') === false) {

                $current_ctype_category = cmsModel::getCachedResult('current_ctype_category');
                if (!empty($current_ctype_category['id'])) {
                    $active_cat = $current_ctype_category;
                }

            } else {

                $item = cmsModel::getCachedResult('current_ctype_item');
                if(!$item){ return false; }

                if (!empty($item['category'])) {
                    $active_cat = $item['category'];
                }

            }

        } else { // проверка, если показ категорий отключен

            $model  = cmsCore::getModel('content');
            $_ctype = $model->getContentTypeByName($ctype_name);
            if (!$_ctype['is_cats']) {
                return false;
            }
        }

        $model = isset($model) ? $model : cmsCore::getModel('content');

        $cats = $model->getCategoriesTree($ctype_name, $this->getOption('is_root'));
        if (!$cats) { return false; }

        if ($active_cat) {

            $path = array_filter($cats, function($cat) use($active_cat) {
                return ($cat['ns_left'] <= $active_cat['ns_left'] &&
                        $cat['ns_level'] <= $active_cat['ns_level'] &&
                        $cat['ns_right'] >= $active_cat['ns_right'] &&
                        $cat['ns_level'] > 0);
            });

        }

        $ctype_default = cmsConfig::get('ctype_default');

        return array(
            'ctype_name'   => (($ctype_default && in_array($ctype_name, $ctype_default)) ? '' : $ctype_name),
            'cats'         => $cats,
            'active_cat'   => $active_cat,
            'cover_preset' => $this->getOption('cover_preset'),
            'path'         => (!empty($path) ? $path : array())
        );

    }

}
