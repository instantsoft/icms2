<?php

class widgetContentCategoryInfo extends cmsWidget {

    public $is_cacheable = false;

    public function run() {

        $ctype    = cmsModel::getCachedResult('current_ctype');
        $category = cmsModel::getCachedResult('current_ctype_category');
        $dataset  = cmsModel::getCachedResult('current_ctype_dataset');

        if (!$category || !$ctype) {
            return false;
        }

        return [
            'dataset'  => $dataset,
            'category' => $category,
            'ctype'    => $ctype
        ];
    }

}
