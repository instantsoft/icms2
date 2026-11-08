<?php

class widgetContentDatasets extends cmsWidget {

    public $is_cacheable = false;

    public function run() {

        $ctype    = cmsModel::getCachedResult('current_ctype');
        $category = cmsModel::getCachedResult('current_ctype_category');
        $dataset  = cmsModel::getCachedResult('current_ctype_dataset');
        $datasets = cmsModel::getCachedResult('current_ctype_datasets');

        if (!$category || !$ctype || !$datasets) {
            return false;
        }

        $base_ds_url = href_to_rel($ctype['name']) . '%s' . (isset($category['slug']) ? '/' . $category['slug'] : '');

        return [
            'ds_prefix'   => '-',
            'base_ds_url' => $base_ds_url,
            'datasets'    => $datasets,
            'dataset'     => $dataset,
            'category'    => $category,
            'ctype'       => $ctype
        ];
    }

}
