<?php

class widgetContentCategories extends cmsWidget {

    public $is_cacheable = false;

    public function run() {

        $ctype = cmsModel::getCachedResult('current_ctype');

        $ctype_name = $this->getOption('ctype_name');

        $active_cat = false;
        $path       = [];

        if (!$ctype_name) {

            if (!$ctype) {
                return false;
            }

            $ctype_name = $ctype['name'];
        }

        if ($ctype && $ctype['name'] === $ctype_name) {

            if (!$ctype['is_cats']) {
                return false;
            }

            if (strpos(cmsCore::getInstance()->uri, '.html') === false) {

                $current_ctype_category = cmsModel::getCachedResult('current_ctype_category');
                if (!empty($current_ctype_category['id'])) {
                    $active_cat = $current_ctype_category;
                }
            } else {

                $item = cmsModel::getCachedResult('current_ctype_item');
                if (!$item) {
                    return false;
                }

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

        $cats = $model->filterIsNull('is_hidden')->getCategoriesTree($ctype_name, $this->getOption('is_root'));
        if (!$cats) {
            return false;
        }

        if ($active_cat) {

            $path = array_filter($cats, function ($cat) use ($active_cat) {
                return ($cat['ns_left'] <= $active_cat['ns_left'] &&
                $cat['ns_level'] <= $active_cat['ns_level'] &&
                $cat['ns_right'] >= $active_cat['ns_right'] &&
                $cat['ns_level'] > 0);
            });
        }

        // считаем вручную кол-во вложенных
        // т.к. у нас могут быть скрытые категории
        // не используем ($cat['ns_right'] - $cat['ns_left']) - 1
        $childs_count = [];

        // результирующее дерево
        $tree = [];

        $show_full_tree = $this->getOption('show_full_tree');
        $cover_preset   = $this->getOption('cover_preset');

        foreach ($cats as $cat) {

            if ($cat['parent_id'] > 1) {
                if (!isset($childs_count[$cat['parent_id']])) {
                    $childs_count[$cat['parent_id']] = 1;
                } else {
                    $childs_count[$cat['parent_id']] += 1;
                }
            }

            $cat['is_active'] = $cat['is_hidden'] = false;

            $cat['childs_count'] = 0;
            $cat['img_src']      = html_image_src($cat['cover'], $cover_preset, true);

            $css_classes = [];

            if (!empty($active_cat['id']) && $cat['id'] == $active_cat['id']) {
                $css_classes[]    = 'active'; // Совместимость cо старыми шаблонами
                $cat['is_active'] = true;
            }

            if (!(isset($path[$cat['id']]) || isset($path[$cat['parent_id']]) || $cat['ns_level'] <= 1) && !$show_full_tree) {
                $css_classes[]    = 'folder_hidden'; // Совместимость cо старыми шаблонами
                $cat['is_hidden'] = true;
            }

            if ($cat['img_src']) {
                $css_classes[] = 'set_cover_preset';
            }

            $cat['css_classes'] = $css_classes;

            $tree[$cat['id']] = $cat;
        }

        if ($childs_count) {
            foreach ($childs_count as $id => $count) {
                if (isset($tree[$id])) {

                    $tree[$id]['childs_count'] = $count;

                    if ($count) {
                        $tree[$id]['css_classes'][] = 'folder';
                    }
                }
            }
        }

        $ctype_default = cmsConfig::get('ctype_default');

        return [
            'ctype_name'   => (($ctype_default && in_array($ctype_name, $ctype_default)) ? '' : $ctype_name),
            'cats'         => $tree,
            'active_cat'   => $active_cat, // в шаблоне не используется, совместимость
            'cover_preset' => $cover_preset,
            'path'         => (!empty($path) ? $path : array()) // в шаблоне не используется, совместимость
        ];
    }

}
