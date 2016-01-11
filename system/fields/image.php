<?php

class fieldImage extends cmsFormField {

    public $title = LANG_PARSER_IMAGE;
    public $sql   = 'text';
	public $allow_index = false;

    private $teaser_url = '';

    public function getOptions(){

		$presets = cmsCore::getModel('images')->getPresetsList();
		$presets['original'] = LANG_PARSER_IMAGE_SIZE_ORIGINAL;

        return array(
            new fieldList('size_teaser', array(
                'title' => LANG_PARSER_IMAGE_SIZE_TEASER,
                'default' => 'small',
                'items' => $presets
            )),
            new fieldList('size_full', array(
                'title' => LANG_PARSER_IMAGE_SIZE_FULL,
                'default' => 'big',
                'items' => $presets
            )),
            new fieldListMultiple('sizes', array(
                'title' => LANG_PARSER_IMAGE_SIZE_UPLOAD,
                'default' => 0,
                'items' => $presets
            )),
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

        if (!$paths || !isset($paths[ $this->getOption('size_full') ])){ return ''; }

        return html_image($paths, $this->getOption('size_full'), (empty($this->item['title']) ? $this->name : $this->item['title']));

    }

    public function store($value, $is_submitted, $old_value=null){

        $config = cmsConfig::getInstance();

        if (!is_null($old_value) && !is_array($old_value)){

            $old_value = cmsModel::yamlToArray($old_value);

            if ($old_value != $value){
                foreach($old_value as $image_url){
                    $image_path = $config->upload_path . $image_url;
                    @unlink($image_path);
                }
            }

        }

        $sizes = $this->getOption('sizes');

        if (empty($sizes) || empty($value)) { return $value; }

        foreach($value as $size => $image_url){
            if (!in_array($size, $sizes)){
                $image_path = $config->upload_path . $image_url;
                @unlink($image_path);
            }
        }

        return $value;

    }

    public function delete($value){

        if (empty($value)) { return true; }

        if (!is_array($value)){ $value = cmsModel::yamlToArray($value); }

        $config = cmsConfig::getInstance();

        foreach($value as $image_url){
            $image_path = $config->upload_path . $image_url;
            @unlink($image_path);
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

        $this->data['images_controller'] = cmsCore::getController('images');

        return parent::getInput($value);

    }

}
