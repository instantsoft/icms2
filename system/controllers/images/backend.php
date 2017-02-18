<?php

class backendImages extends cmsBackend{

    private $default_images_types = array('private');

    public function actionIndex(){
        $this->redirectToAction('presets');
    }

    public function deleteDefaultImages($preset) {

        $default_root = cmsConfig::get('upload_path').'default/';

        foreach ($this->default_images_types as $image_type) {
            @unlink($default_root.$image_type . '_' . $preset['name'] . '.png');
        }

        return true;

    }

    public function createDefaultImages($preset) {

        if (!empty($preset['is_internal'])){
            return false;
        }

        $default_root = cmsConfig::get('upload_path').'default/';

        foreach ($this->default_images_types as $image_type) {

            $file_name     = $image_type . '_' . $preset['name'] . '.png';
            $original_file = $image_type . '_original.png';

            $create = true;

            // если такой файл есть, смотрим размер изображения, если изменился, удаляем и создаем новый
            if(file_exists($default_root.$file_name)){

                $create = false;

                $size = getimagesize($default_root.$file_name);

                // проверяем только заданные в пресете размеры
                if($preset['width'] && $preset['width'] != $size[0]){
                    $create = true;
                }
                if($preset['height'] && $preset['height'] != $size[1]){
                    $create = true;
                }

                if($create){
                    @unlink($default_root.$file_name);
                }

            }

            if($create){

                if(!file_exists($default_root.$original_file)){
                    return false;
                }

                if (!isset($preset['height'])) { $preset['height'] = $preset['width']; }

                img_resize($default_root.$original_file, $default_root.$file_name, $preset['width'], $preset['height'], $preset['is_square'], 30);

            }

            return true;

        }

    }

}
