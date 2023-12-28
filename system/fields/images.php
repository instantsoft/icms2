<?php

class fieldImages extends cmsFormField {

    public $title       = LANG_PARSER_IMAGES;
    public $sql         = 'text';
    public $allow_index = false;
    public $var_type    = 'array';

    public function getOptions() {

        $preset_generator = function () {

            static $presets = [];

            if(!$presets){

                $presets = cmsCore::getModel('images')->getPresetsList(true);
                $presets['original'] = LANG_PARSER_IMAGE_SIZE_ORIGINAL;
            }

            return $presets;
        };

        return [
            new fieldList('size_teaser', [
                'title'     => LANG_PARSER_IMAGE_SIZE_TEASER,
                'default'   => 'small',
                'generator' => $preset_generator,
                'extended_option' => true
            ]),
            new fieldList('size_small', [
                'title'     => LANG_PARSER_IMAGE_SIZE_FULL,
                'default'   => 'small',
                'generator' => $preset_generator,
                'extended_option' => true
            ]),
            new fieldList('size_full', [
                'title'     => LANG_PARSER_IMAGE_SIZE_MODAL,
                'default'   => 'big',
                'generator' => $preset_generator
            ]),
            new fieldListMultiple('sizes', [
                'title'     => LANG_PARSER_IMAGE_SIZE_UPLOAD,
                'default'   => 0,
                'generator' => $preset_generator,
                'rules' => [['required']]
             ]),
            new fieldCheckbox('allow_import_link', [
                'title' => LANG_PARSER_IMAGE_ALLOW_IMPORT_LINK
            ]),
            new fieldCheckbox('first_image_emphasize', [
                'title' => LANG_PARSER_FIRST_IMAGE_EMPHASIZE
            ]),
            new fieldCheckbox('view_as_slider', [
                'title' => LANG_PARSER_IMAGE_VIEW_AS_SLIDER
            ]),
            new fieldCheckbox('slider_dots', [
                'title' => LANG_PARSER_IMAGE_SLIDER_DOTS,
                'visible_depend' => ['options:view_as_slider' => ['show' => ['1']]]
            ]),
            new fieldNumber('max_photos', [
                'title' => LANG_PARSER_IMAGE_MAX_COUNT
            ]),
            new fieldList('template', [
                'title'     => LANG_PARSER_TEMPLATE,
                'hint'      => sprintf(LANG_PARSER_TEMPLATE_HINT, 'assets/fields/', $this->field_type.'_view'),
                'generator' => function () {
                    return cmsTemplate::getInstance()->getAvailableTemplatesFiles('assets/fields', $this->field_type.'_view*.tpl.php');
                }
            ])
        ];
    }

    /**
     * Генератор списка изображений
     * @param array $images
     * @return Generator
     */
    public function parseGenerator(array $images) {

        $is_set_first = false;

        foreach ($images as $key => $paths) {

            if (!isset($paths[$this->getOption('size_full')])) {
                continue;
            }

            $image = [
                'title'        => (empty($this->item['title']) ? $this->name : $this->item['title']),
                'paths'        => $paths,
                'small_preset' => $this->is_parse_teaser ? $this->getOption('size_teaser') : $this->getOption('size_small'),
                'big_preset'   => $this->getOption('size_full'),
                'is_gif'       => false,
                'link_class'   => 'img-' . $this->name,
                'img_class'    => 'img-thumbnail'
            ];

            if ($this->getOption('first_image_emphasize') && !$is_set_first) {

                $image['small_preset'] = $this->getOption('size_full');
                $image['link_class']  .= ' first_type_images';

                $is_set_first = true;

            } else {
                $image['link_class'] .= ' second_type_images';
            }

            if (!empty($paths['original']) && strtolower(pathinfo($paths['original'], PATHINFO_EXTENSION)) === 'gif') {

                $image['is_gif']    = true;
                $image['img_class'] = 'img-' . $this->name;

            }

            yield $key => $image;
        }

        return isset($image) ? true : false;
    }

    public function parse($value) {

        if (is_empty_value($value)) {
            return '';
        }

        if (!is_array($value)) {
            $value = cmsModel::yamlToArray($value);
        }

        return cmsTemplate::getInstance()->renderFormField($this->getOption('template', $this->class . '_view'), [
            'block_id' => 'slider-' . uniqid(),
            'slider_params' => [
                'dots'           => (bool)$this->getOption('slider_dots'),
                'variableWidth'  => true,
                'adaptiveHeight' => true,
                'infinite'       => false,
                'arrows'         => false,
                'slidesToScroll' => 2
            ],
            'field'  => $this,
            'images' => $this->parseGenerator($value)
        ]);
    }

    public function getStringValue($value) {
        return '';
    }

    public function store($value, $is_submitted, $old_value = null) {

        if (!is_array($old_value)) {
            $old_value = cmsModel::yamlToArray($old_value);
        }

        foreach ($old_value as $old_image) {
            if (!is_array($value) || !in_array($old_image, $value)) {
                foreach ($old_image as $size => $image_rel_path) {
                    files_delete_file($image_rel_path, 2);
                }
            }
        }

        if (!$value) {
            return null;
        }

        $sizes = $this->getOption('sizes');
        if (!$sizes) {
            $this->delete($value);
            return null;
        }

        $results = [];

        $upload_path = realpath(cmsConfig::get('upload_path')).DIRECTORY_SEPARATOR;

        foreach ($value as $key => $image) {

            if (!is_array($image)) {
                continue;
            }

            $images = [];

            foreach ($image as $size => $image_rel_path) {

                if (is_array($image_rel_path)) {
                    continue;
                }

                $image_rel_path = str_replace(['"', "'", ' ', '#'], '', html_entity_decode($image_rel_path));

                $image_path = realpath($upload_path . $image_rel_path);

                if (strpos($image_path, $upload_path) !== 0 || !is_file($image_path)) {
                    continue;
                }

                // удаляем ненужные пресеты, если умельцы правили параметры вручную
                if (!in_array($size, $sizes)) {
                    files_delete_file($image_rel_path, 2);
                    continue;
                }

                $images[$size] = $image_rel_path;
            }

            if ($images) {
                $results[$key] = $images;
            }
        }

        if (empty($results)) {
            return null;
        }

        // удаляем, если вдруг каким-то образом загрузили больше
        // js тоже регулирует этот параметр
        if (!empty($this->options['max_photos']) && count($results) > $this->options['max_photos']) {

            $chunks  = array_chunk($results, $this->options['max_photos'], true);
            $results = $chunks[0];
            unset($chunks[0]);

            foreach ($chunks as $chunk) {
                $this->delete($chunk);
            }
        }

        return $results;
    }

    public function getFiles($value) {

        if (empty($value)) {
            return false;
        }

        if (!is_array($value)) {
            $value = cmsModel::yamlToArray($value);
        }

        $files = [];

        foreach ($value as $images) {
            foreach ($images as $image_rel_path) {
                $files[] = $image_rel_path;
            }
        }

        return $files;
    }

    public function delete($value) {

        if (!$value) {
            return true;
        }

        if (!is_array($value)) {

            $value = cmsModel::yamlToArray($value);

            if (!$value) {
                return true;
            }
        }

        $upload_path = cmsConfig::get('upload_path');

        $files_model = cmsCore::getModel('files');

        foreach ($value as $images) {
            if (!is_array($images)) {
                continue;
            }
            foreach ($images as $image_rel_path) {

                if (is_array($image_rel_path)) {
                    continue;
                }

                $image_path = realpath($upload_path . $image_rel_path);

                if (strpos($image_path, $upload_path) !== 0 || !is_file($image_path)) {
                    continue;
                }

                $file = $files_model->getFileByPath($image_rel_path);
                if (!$file) {
                    files_delete_file($image_rel_path, 2);
                    continue;
                }

                $files_model->deleteFile($file['id']);
            }
        }

        return true;
    }

    public function getFilterInput($value = false) {
        return html_checkbox($this->name, (bool) $value);
    }

    public function applyFilter($model, $value) {
        return $model->filterNotNull($this->name);
    }

    public function getInput($value) {

        $this->data['images'] = false;

        if ($value) {
            $this->data['images'] = is_array($value) ? $value : cmsModel::yamlToArray($value);
        }

        $this->data['id']                = $this->id;
        $this->data['sizes']             = $this->getOption('sizes');
        $this->data['allow_import_link'] = $this->getOption('allow_import_link');
        $this->data['max_photos']        = $this->getOption('max_photos');

        $this->data['images_controller'] = cmsCore::getController('images', new cmsRequest($this->context_params, cmsRequest::CTX_INTERNAL));

        return parent::getInput($value);
    }

}
