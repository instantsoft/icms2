<?php

class fieldImage extends cmsFormField {

    public $title       = LANG_PARSER_IMAGE;
    public $sql         = 'text';
    public $allow_index = false;
    public $var_type    = 'array';
    protected $teaser_url = '';

    public function getOptions(){

        return array(
            new fieldList('size_teaser', array(
                'title'     => LANG_PARSER_IMAGE_SIZE_TEASER,
                'default'   => 'small',
                'generator' => function (){
                    $presets = cmsCore::getModel('images')->getPresetsList(true);
                    $presets['original'] = LANG_PARSER_IMAGE_SIZE_ORIGINAL;
                    return $presets;
                }
            )),
            new fieldList('size_full', array(
                'title'     => LANG_PARSER_IMAGE_SIZE_FULL,
                'default'   => 'big',
                'generator' => function (){
                    $presets = cmsCore::getModel('images')->getPresetsList(true);
                    $presets['original'] = LANG_PARSER_IMAGE_SIZE_ORIGINAL;
                    return $presets;
                }
            )),
            new fieldList('size_modal', array(
                'title'     => LANG_PARSER_IMAGE_SIZE_MODAL,
                'default'   => '',
                'generator' => function (){
                    $presets = cmsCore::getModel('images')->getPresetsList(true);
                    $presets['original'] = LANG_PARSER_IMAGE_SIZE_ORIGINAL;
                    return array('' => '') + $presets;
                }
            )),
            new fieldListMultiple('sizes', array(
                'title'     => LANG_PARSER_IMAGE_SIZE_UPLOAD,
                'default'   => 0,
                'generator' => function (){
                    $presets = cmsCore::getModel('images')->getPresetsList();
                    $presets['original'] = LANG_PARSER_IMAGE_SIZE_ORIGINAL;
                    return $presets;
                }
            )),
            new fieldCheckbox('allow_import_link', array(
                'title' => LANG_PARSER_IMAGE_ALLOW_IMPORT_LINK
            ))
        );

    }

    public function setTeaserURL($url){
        $this->teaser_url = $url;
        return $this;
    }

    public function parseTeaser($value){

        $paths = is_array($value) ? $value : cmsModel::yamlToArray($value);

        if (!$paths && $this->hasDefaultValue()){ $paths = $this->parseDefaultPaths(); }

        if (!$paths || !isset($paths[ $this->getOption('size_teaser') ])){ return ''; }

        $url = $this->teaser_url ?
                $this->teaser_url :
                href_to($this->item['ctype']['name'], $this->item['slug'] . ".html");

        return '<a href="'.$url.'">'.html_image($paths, $this->getOption('size_teaser'), (empty($this->item['title']) ? $this->name : $this->item['title'])).'</a>';

    }

    public function parse($value){

        $paths = is_array($value) ? $value : cmsModel::yamlToArray($value);

        if (!$paths && $this->hasDefaultValue()){ $paths = $this->parseDefaultPaths(); }

        $size_full = $this->getOption('size_full');
        $size_modal = $this->getOption('size_modal');

        if (!$paths || !isset($paths[ $size_full ])){ return ''; }

        $presets = array($size_full, false);

        if(!empty($paths['original']) &&  strtolower(pathinfo($paths['original'], PATHINFO_EXTENSION)) === 'gif'){
            $img_func = 'html_gif_image';
        } else {
            $img_func = 'html_image';
            if($size_modal){ $presets[1] = $size_modal; }
        }

        return $img_func($paths, $presets, (empty($this->item['title']) ? $this->name : $this->item['title']));

    }

    public function store($value, $is_submitted, $old_value=null){

        if (!is_null($old_value) && !is_array($old_value)){

            $old_value = cmsModel::yamlToArray($old_value);

            if ($old_value != $value){
                foreach($old_value as $image_url){
                    files_delete_file($image_url, 2);
                }
            }

        }

        $sizes = $this->getOption('sizes');

        if (empty($sizes) || empty($value)) { return $value; }

        $upload_path = cmsConfig::get('upload_path');

        $image_urls = array();

        foreach($value as $size => $image_url){

            $image_url = str_replace(array('"', "'", ' ', '#'), '', html_entity_decode($image_url));

            if(!is_file($upload_path.$image_url)){
                continue;
            }

            if (!in_array($size, $sizes)){
                files_delete_file($image_url, 2); continue;
            }

            $image_urls[$size] = $image_url;

        }

        return $image_urls ?: null;

    }

    public function delete($value){

        if (empty($value)) { return true; }

        if (!is_array($value)){ $value = cmsModel::yamlToArray($value); }

        foreach($value as $image_url){
            files_delete_file($image_url, 2);
        }

        return true;

    }

    public function parseDefaultPaths(){
        $string = $this->getDefaultValue();
        if (!$string) { return false; }
        $items = array();
        $rows = explode("\n", $string);
        if (is_array($rows)){
            foreach($rows as $row){
                $item = explode('|', trim($row));
                $items[trim($item[0])] = trim($item[1]);
            }
        }
        return $items;
    }

    public function getFilterInput($value=false) {

        return html_checkbox($this->name, (bool)$value);

    }

    public function applyFilter($model, $value) {
        return $model->filterNotNull($this->name);
    }

    public function getInput($value){

        $this->data['paths'] = false;

        if($value){
            $this->data['paths'] = is_array($value) ? $value : cmsModel::yamlToArray($value);
        }

        $this->data['sizes'] = $this->getOption('sizes');
        $this->data['allow_import_link'] = $this->getOption('allow_import_link');

        $this->data['images_controller'] = cmsCore::getController('images');

        return parent::getInput($value);

    }

}
