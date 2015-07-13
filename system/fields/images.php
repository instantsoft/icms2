<?php

class fieldImages extends cmsFormField {

    public $title = LANG_PARSER_IMAGES;
    public $sql   = 'text';
	public $allow_index = false;

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

    public function parseTeaser($value){

        $config = cmsConfig::getInstance();

        $images = cmsModel::yamlToArray($value);

        $html = '';

        foreach($images as $paths){
            $html .= '<a href="'.$config->upload_host . '/' . $paths[$this->getOption('size_full')].'"><img src="'.$config->upload_host . '/' . $paths['small'].'" border="0" /></a>';
            break;
        }

        return $html;

    }

    public function parse($value){

        $config = cmsConfig::getInstance();

        $images = cmsModel::yamlToArray($value);

        $html = '';

        foreach($images as $paths){
			$full = isset($paths[$this->getOption('size_full')]) ? $paths[$this->getOption('size_full')] : '';
            $html .= '<a class="img-'.$this->getName().'" href="'.$config->upload_host . '/' . $full.'"><img src="'.$config->upload_host . '/' . $paths['small'].'" border="0" /></a>';
        }

        $html .= '<script>$(document).ready(function() { icms.modal.bindGallery(".img-'.$this->getName().'"); });</script>';

        return $html;

    }

    public function store($value, $is_submitted, $old_value=null){

        $result = null;

        if (is_array($value)){
            $result = array();
            foreach ($value as $idx=>$paths){ $result[] = $paths; }
        }

        $sizes = $this->getOption('sizes');

        if (empty($sizes) || empty($result)) { return $result; }

        $config = cmsConfig::getInstance();

        foreach($result as $image){
            foreach($image as $size => $image_url){
                if (!in_array($size, $sizes)){
                    $image_path = $config->upload_path . $image_url;
                    @unlink($image_path);
                }
            }
        }
        return $result;

    }
    
    public function delete($value){
        
        if (empty($value)) { return true; }
        
        if (!is_array($value)){ $value = cmsModel::yamlToArray($value); }
        
        $config = cmsConfig::getInstance();
        
        foreach($value as $images){
            foreach($images as $image_url){
                $image_path = $config->upload_path . $image_url;
                @unlink($image_path);
            }
        }
        
        return true;
        
    }

    public function getFilterInput($value=false) {

        return html_checkbox($this->name, (bool)$value);

    }

    public function applyFilter($model, $value) {
        return $model->filterNotNull($this->name);
    }

}
