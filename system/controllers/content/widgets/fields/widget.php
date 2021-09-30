<?php

class widgetContentFields extends cmsWidget {

    public $is_cacheable = false;

    public function run() {

        $item   = cmsModel::getCachedResult('current_ctype_item');
        $ctype  = cmsModel::getCachedResult('current_ctype');
        $fields = cmsModel::getCachedResult('current_ctype_fields');

        if (!$item || !$ctype || !$fields) {
            return false;
        }

        $image_path        = $this->getOption('image_path');
        $show_info_block   = $this->getOption('show_info_block');
        $image_field       = $this->getOption('image_field');
        $image_preset      = $this->getOption('image_preset');
        $fields_names      = array_filter($this->getOption('fields', []));
        $image_is_parallax = $this->getOption('image_is_parallax');

        if (!$fields_names && !$image_field) {
            return false;
        }

        $widget_fields = [];

        if ($fields_names) {
            foreach ($fields_names as $name) {
                if(isset($fields[$name])){
                    $widget_fields[$name] = $fields[$name];
                }
            }
        }

        $image_src = '';

        if (!empty($item[$image_field])) {

            $image = cmsModel::yamlToArray($item[$image_field]);

            if (isset($image[$image_preset])) {
                $image_src = html_image_src($image, $image_preset, true);
            }
        }

        if(!$image_src && $image_path){
            $image_src = html_image_src($image_path, 'original', true);
        }

        return [
            'show_info_block'   => $show_info_block,
            'image_is_parallax' => $image_is_parallax,
            'image_src'         => $image_src,
            'fields'            => $widget_fields,
            'ctype'             => $ctype,
            'item'              => $item
        ];
    }

}
