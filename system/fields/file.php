<?php

class fieldFile extends cmsFormField {

    public $title = LANG_PARSER_FILE;
    public $sql   = 'text';

    public function getOptions(){
        $max_size = files_convert_bytes(ini_get('post_max_size')) / 1048576;
        return array(
            new fieldList('show_name', array(
                'title' => LANG_PARSER_FILE_LABEL,
                'default' => 1,
                'items' => array(
                    0 => LANG_PARSER_FILE_LABEL_GET,
                    1 => LANG_PARSER_FILE_LABEL_NAME
                )
            )),
            new fieldString('extensions', array(
                'title' => LANG_PARSER_FILE_EXTS,
                'hint'  => LANG_PARSER_FILE_EXTS_HINT
            )),
            new fieldNumber('max_size_mb', array(
                'title' => LANG_PARSER_FILE_MAX_SIZE,
                'hint'  => sprintf(LANG_PARSER_FILE_MAX_SIZE_PHP, $max_size)
            )),
            new fieldCheckbox('show_size', array(
                'title' => LANG_PARSER_FILE_SHOW_SIZE
            )),
            new fieldCheckbox('show_counter', array(
                'title' => LANG_PARSER_FILE_SHOW_COUNTER
            ))
        );
    }

    public function parse($value){

        $file = is_array($value) ? $value : cmsModel::yamlToArray($value);
        if (!$file){ return ''; }

        $size_counter = $size_info = '';

        if($this->getOption('show_counter')){

            $file = cmsCore::getModel('files')->getFile($file['id']);
            if (!$file){ return ''; }

            if($file['counter']){
                $size_counter = '<span class="size">'.LANG_PARSER_FILE_LABEL_COUNTER.' '
                        .html_spellcount($file['counter'], LANG_TIME1, LANG_TIME2, LANG_TIME10).'</span>';
            }

        }

        if($this->getOption('show_size')){
            $size_info = '<span class="size">'.files_format_bytes($file['size']).'</span>';
        }

        $name = $this->getOption('show_name') ? $file['name'] : LANG_PARSER_FILE_LABEL_GET;

        return '<a href="'.$this->getDownloadURL($file).'">'.$name.'</a> ' . $size_info . $size_counter;

    }

    public function getStringValue($value){

        $file = is_array($value) ? $value : cmsModel::yamlToArray($value);
        if (!$file){ return ''; }

        return $this->getOption('show_name') ? $file['name'] : LANG_PARSER_FILE_LABEL_GET;

    }

    public function getDownloadURL($file){

        return href_to('files', 'download', array($file['id'], files_user_file_hash($file['path'])));

    }

    public function store($value, $is_submitted, $old_value=null){

        $files_model = cmsCore::getModel('files');

        if ($value && $old_value){

            $this->delete($old_value);

            $old_value = null;

        }

        $uploader = new cmsUploader();
        $core = cmsCore::getInstance();

        if (!$uploader->isUploaded($this->name)){
            return $old_value;
        }

        $allowed_extensions = $this->getOption('extensions');
        $max_size_mb = $this->getOption('max_size_mb');

        if (!trim($allowed_extensions)) { $allowed_extensions = false; }
        if (!$max_size_mb) { $max_size_mb = 0; }

        $result = $uploader->upload($this->name, $allowed_extensions, $max_size_mb * 1048576);

        if (!$result['success']){
            if (!empty($result['path'])){
				$uploader->remove($result['path']);
			}
            cmsUser::addSessionMessage($result['error'], 'error');
            return null;
        }

        $context = $core->getUriData();
        $upload_params = array();

        if($context['controller']){
            $upload_params['target_controller'] = $context['controller'];
        }
        if($context['action']){
            $upload_params['target_subject'] = $context['action'];
        }
        if(strpos($core->uri, '/add/') === false && !empty($context['params'][0]) && is_numeric($context['params'][0])){
            $upload_params['target_id'] = $context['params'][0];
        }

        $file_id = $files_model->registerFile(array_merge($upload_params, array(
            'path'    => $result['url'],
            'name'    => $result['name'],
            'user_id' => cmsUser::get('id')
        )));

        return array(
            'id'   => $file_id,
            'name' => $result['name'],
            'size' => $result['size'],
            'path' => $result['url']
        );

    }

    public function delete($value){

        if (empty($value)) { return true; }

        if (!is_array($value)){ $value = cmsModel::yamlToArray($value); }

        cmsCore::getModel('files')->deleteFile($value['id']);

        return true;

    }

    public function getFilterInput($value=false) {

        return ($this->show_filter_input_title ? '<label for="'.$this->id.'">'.$this->title.'</label>' : '') .
               html_checkbox($this->name, (bool)$value);

    }

    public function applyFilter($model, $value) {
        return $model->filterNotNull($this->name);
    }

    public function getInput($value){

        $this->data['allowed_extensions']   = $this->getOption('extensions');
        $this->data['max_size_mb']          = $this->getOption('max_size_mb');

        if($this->data['max_size_mb']){
            $this->data['max_size_mb'] *= 1048576;
        }else{
            $this->data['max_size_mb'] = files_convert_bytes(ini_get('post_max_size'));
        }

        return parent::getInput($value);

    }

}
