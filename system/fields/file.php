<?php

class fieldFile extends cmsFormField {

    public $title = LANG_PARSER_FILE;
    public $sql   = 'text';

    private $validate_error = true;

    public function getOptions() {
        $max_size = files_convert_bytes(ini_get('post_max_size')) / 1048576;
        return [
            new fieldList('show_name', [
                'title'   => LANG_PARSER_FILE_LABEL,
                'default' => 1,
                'items'   => [
                    0 => LANG_PARSER_FILE_LABEL_GET,
                    1 => LANG_PARSER_FILE_LABEL_NAME
                ],
                'extended_option' => true
            ]),
            new fieldString('extensions', [
                'title' => LANG_PARSER_FILE_EXTS,
                'hint'  => LANG_PARSER_FILE_EXTS_HINT
            ]),
            new fieldNumber('max_size_mb', [
                'title' => LANG_PARSER_FILE_MAX_SIZE,
                'hint'  => sprintf(LANG_PARSER_FILE_MAX_SIZE_PHP, $max_size)
            ]),
            new fieldCheckbox('show_size', [
                'title'           => LANG_PARSER_FILE_SHOW_SIZE,
                'extended_option' => true
            ]),
            new fieldCheckbox('show_counter', [
                'title'           => LANG_PARSER_FILE_SHOW_COUNTER,
                'extended_option' => true
            ])
        ];
    }

    public function getRules() {

        $this->rules[] = ['file'];

        return $this->rules;
    }

    public function parse($value) {

        $file = is_array($value) ? $value : cmsModel::yamlToArray($value);
        if (!$file) { return ''; }

        $size_counter = $size_info = '';

        if ($this->getOption('show_counter')) {

            $file = cmsCore::getModel('files')->getFile($file['id']);
            if (!$file) { return ''; }

            if ($file['counter']) {
                $size_counter = '<span class="size ml-2">' . LANG_PARSER_FILE_LABEL_COUNTER . ' '
                        . html_spellcount($file['counter'], LANG_TIME1, LANG_TIME2, LANG_TIME10) . '</span>';
            }
        }

        if ($this->getOption('show_size')) {
            $size_info = '<span class="size ml-2">' . files_format_bytes($file['size']) . '</span>';
        }

        $name = $this->getOption('show_name') ? $file['name'] : LANG_PARSER_FILE_LABEL_GET;

        return '<a href="' . $this->getDownloadURL($file) . '">' . $name . '</a> ' . $size_info . $size_counter;
    }

    public function getStringValue($value) {
        return '';
    }

    public function getDownloadURL($file) {
        return href_to('files', 'download', [$file['id'], files_user_file_hash($file['path'])]);
    }

    public function store($value, $is_submitted, $old_value = null) {

        $files_model = cmsCore::getModel('files');

        if ($value && $old_value) {

            $this->delete($old_value);

            $old_value = null;
        }

        $uploader = new cmsUploader();
        $core = cmsCore::getInstance();

        if (!$uploader->isUploaded($this->name)) {
            return $old_value;
        }

        $allowed_extensions = $this->getOption('extensions');
        $max_size_mb        = $this->getOption('max_size_mb');

        if (!trim($allowed_extensions)) {
            $allowed_extensions = false;
        }
        if (!$max_size_mb) {
            $max_size_mb = 0;
        }

        $result = $uploader->upload($this->name, $allowed_extensions, $max_size_mb * 1048576);

        if (!$result['success']) {
            if (!empty($result['path'])) {
                $uploader->remove($result['path']);
            }
            $this->validate_error = $result['error'];
            return null;
        }

        $context = $core->getUriData();
        $upload_params = [];

        if ($context['controller']) {
            $upload_params['target_controller'] = $context['controller'];
        }
        if ($context['action']) {
            $upload_params['target_subject'] = $context['action'];
        }
        if (strpos($core->uri, '/add/') === false && !empty($context['params'][0]) && is_numeric($context['params'][0])) {
            $upload_params['target_id'] = $context['params'][0];
        }

        $file_id = $files_model->registerFile(array_merge($upload_params, [
            'path'    => $result['url'],
            'name'    => $result['name'],
            'user_id' => cmsUser::get('id')
        ]));

        return [
            'id'   => $file_id,
            'name' => $result['name'],
            'size' => $result['size'],
            'path' => $result['url']
        ];
    }

    public function getFiles($value) {

        if (empty($value)) {
            return false;
        }

        if (!is_array($value)) {
            $value = cmsModel::yamlToArray($value);
        }

        return [$value['path']];
    }

    public function delete($value) {

        if (empty($value)) { return true; }

        if (!is_array($value)) {
            $value = cmsModel::yamlToArray($value);
        }

        cmsCore::getModel('files')->deleteFile($value['id']);

        return true;
    }

    public function getFilterInput($value = false) {
        return ($this->show_filter_input_title ? '<label for="' . $this->id . '">' . $this->title . '</label>' : '') .
                html_checkbox($this->name, (bool) $value);
    }

    public function applyFilter($model, $value) {
        return $model->filterNotNull($this->name);
    }

    public function getInput($value) {

        $this->data['attributes'] = $this->getProperty('attributes') ?: [];

        $this->data['attributes']['class']    = 'custom-file-input';
        $this->data['attributes']['id']       = $this->id;
        $this->data['attributes']['required'] = !$value && (array_search(['required'], $this->getRules()) !== false);

        $this->data['allowed_extensions'] = $this->getOption('extensions');
        $this->data['max_size_mb']        = $this->getOption('max_size_mb');

        if ($this->data['max_size_mb']) {
            $this->data['max_size_mb'] *= 1048576;
        } else {
            $this->data['max_size_mb'] = files_convert_bytes(ini_get('post_max_size'));
        }

        return parent::getInput($value);
    }

    public function validate_file($value) {
        return $this->validate_error;
    }

    public function validate_required($value) {
        if (is_string($this->validate_error)) {
            return $this->validate_error;
        }
        if (empty($value)) { return ERR_VALIDATE_REQUIRED; }
        return true;
    }

}
