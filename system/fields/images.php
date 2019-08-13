<?php

class fieldImages extends cmsFormField {

    public $title       = LANG_PARSER_IMAGES;
    public $sql         = 'text';
    public $allow_index = false;
    public $var_type    = 'array';

    public function getOptions(){

        return array(
            new fieldList('size_teaser', array(
                'title' => LANG_PARSER_IMAGE_SIZE_TEASER,
                'default' => 'small',
                'generator' => function (){
                    $presets = cmsCore::getModel('images')->getPresetsList(true);
                    $presets['original'] = LANG_PARSER_IMAGE_SIZE_ORIGINAL;
                    return $presets;
                }
            )),
            new fieldList('size_full', array(
                'title' => LANG_PARSER_IMAGE_SIZE_FULL,
                'default' => 'big',
                'generator' => function (){
                    $presets = cmsCore::getModel('images')->getPresetsList(true);
                    $presets['original'] = LANG_PARSER_IMAGE_SIZE_ORIGINAL;
                    return $presets;
                }
            )),
            new fieldList('size_small', array(
                'title'   => LANG_PARSER_SMALL_IMAGE_PRESET,
                'default' => 'small',
                'generator' => function (){
                    $presets = cmsCore::getModel('images')->getPresetsList(true);
                    $presets['original'] = LANG_PARSER_IMAGE_SIZE_ORIGINAL;
                    return $presets;
                }
            )),
            new fieldListMultiple('sizes', array(
                'title' => LANG_PARSER_IMAGE_SIZE_UPLOAD,
                'default' => 0,
                'generator' => function (){
                    $presets = cmsCore::getModel('images')->getPresetsList();
                    $presets['original'] = LANG_PARSER_IMAGE_SIZE_ORIGINAL;
                    return $presets;
                }
            )),
            new fieldCheckbox('allow_import_link', array(
                'title' => LANG_PARSER_IMAGE_ALLOW_IMPORT_LINK
            )),
            new fieldCheckbox('first_image_emphasize', array(
                'title' => LANG_PARSER_FIRST_IMAGE_EMPHASIZE
            )),
            new fieldNumber('max_photos', array(
                'title' => LANG_PARSER_IMAGE_MAX_COUNT
            ))
        );

    }

    public function parse($value){

        $images = is_array($value) ? $value : cmsModel::yamlToArray($value);

        $html         = '';
        $small_preset = false;
        $a_class      = '';

        foreach($images as $key => $paths){

            if (!isset($paths[$this->getOption('size_full')])){ continue; }

            $title = (empty($this->item['title']) ? $this->name : $this->item['title']);

            if($this->getOption('first_image_emphasize') && !$small_preset){
                $small_preset = $this->getOption('size_full');
                $a_class = 'first_type_images';
            } else {
                $small_preset = $this->getOption('size_small');
				$a_class = 'second_type_images';
            }

            if(!empty($paths['original']) &&  strtolower(pathinfo($paths['original'], PATHINFO_EXTENSION)) === 'gif'){
                $html .= html_gif_image($paths, 'small', $title.' '.$key, array('class'=>'img-'.$this->getName()));
            } else {
                $html .= '<a title="'.html($title, false).'" class="img-'.$this->getName().' '.$a_class.'" href="'.html_image_src($paths, $this->getOption('size_full'), true).'">'.html_image($paths, $small_preset, $title.' '.$key, ['class' => 'img-thumbnail']).'</a>';
            }

        }

        if($html){
            cmsTemplate::getInstance()->addBottom('<script>$(function() { icms.modal.bindGallery(".img-'.$this->getName().'"); });</script>');
        }

        return $html;

    }

    public function store($value, $is_submitted, $old_value=null){

		if (!is_array($old_value)){
			$old_value = cmsModel::yamlToArray($old_value);
		}

        foreach($old_value as $old_image){
            if (!is_array($value) || !in_array($old_image, $value)){
                foreach($old_image as $size => $image_rel_path){
                    files_delete_file($image_rel_path, 2);
                }
            }
        }

        $result = array();

        if (is_array($value)){
            foreach ($value as $paths){ $result[] = $paths; }
        }

        if (empty($result)) { return null; }

        $sizes = $this->getOption('sizes');
        if (empty($sizes)) {
            $this->delete($result); return null;
        }

        $results = array();

        $upload_path = cmsConfig::get('upload_path');

        foreach($result as $key => $image){

            $images = array();

            foreach($image as $size => $image_rel_path){

                $image_rel_path = str_replace(array('"', "'", ' ', '#'), '', html_entity_decode($image_rel_path));

                if(!is_file($upload_path.$image_rel_path)){
                    continue;
                }

                // удаляем ненужные пресеты, если умельцы правили параметры вручную
                if (!in_array($size, $sizes)){
                    files_delete_file($image_rel_path, 2); continue;
                }

                $images[$size] = $image_rel_path;

            }

            if($images){
                $results[$key] = $images;
            }

        }

        if (empty($results)) { return null; }

        // удаляем, если вдруг каким-то образом загрузили больше
        // js тоже регулирует этот параметр
        if(!empty($this->options['max_photos']) && count($results) > $this->options['max_photos']){

            $chunks = array_chunk($results, $this->options['max_photos'], true);
            $results = $chunks[0]; unset($chunks[0]);

            foreach ($chunks as $chunk) {
                $this->delete($chunk);
            }

        }

        return $results;

    }

    public function delete($value){

        if (empty($value)) { return true; }

        if (!is_array($value)){ $value = cmsModel::yamlToArray($value); }

        foreach($value as $images){
            foreach($images as $image_rel_path){
                files_delete_file($image_rel_path, 2);
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

    public function getInput($value){

        $this->data['images'] = false;

        if($value){
            $this->data['images'] = is_array($value) ? $value : cmsModel::yamlToArray($value);
        }

        $this->data['sizes'] = $this->getOption('sizes');
        $this->data['allow_import_link'] = $this->getOption('allow_import_link');
        $this->data['max_photos'] = $this->getOption('max_photos');

        $this->data['images_controller'] = cmsCore::getController('images');

        return parent::getInput($value);

    }

}
