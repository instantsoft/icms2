<?php

class fieldHtml extends cmsFormField {

    public $title       = LANG_PARSER_HTML;
    public $sql         = 'mediumtext';
    public $filter_type = 'str';
    public $allow_index = false;
    public $var_type    = 'string';

    public function getOptions(){
        return array(
            new fieldList('editor', array(
                'title' => LANG_PARSER_HTML_EDITOR,
                'default' => cmsConfig::get('default_editor'),
                'generator' => function($item){
                    $items = [];
                    $editors = cmsCore::getWysiwygs();
                    foreach($editors as $editor){
                        $items[$editor] = ucfirst($editor);
                    }
                    $ps = cmsCore::getModel('wysiwygs')->getPresetsList();
                    if($ps){
                        foreach ($ps as $key => $value) {
                            $items[$key] = $value;
                        }
                    }
                    return $items;
                }
            )),
            new fieldList('editor_presets', array(
                'title'        => LANG_PARSER_HTML_EDITOR_GR,
                'is_multiple'  => true,
                'dynamic_list' => true,
                'select_title' => LANG_SELECT,
                'multiple_keys' => array(
                    'group_id' => 'field', 'preset_id' => 'field_select'
                ),
                'generator' => function($item){
                    $users_model = cmsCore::getModel('users');

                    $items = [];

                    $groups = $users_model->getGroups(false);

                    foreach($groups as $group){
                        $items[$group['id']] = $group['title'];
                    }

                    return $items;
                },
                'values_generator' => function() {
                    $items = [];
                    $editors = cmsCore::getWysiwygs();
                    foreach($editors as $editor){
                        $items[$editor] = ucfirst($editor);
                    }
                    $ps = cmsCore::getModel('wysiwygs')->getPresetsList();
                    if($ps){
                        foreach ($ps as $key => $value) {
                            $items[$key] = $value;
                        }
                    }
                    return $items;
                }
            )),
            new fieldCheckbox('is_html_filter', array(
                'title' => LANG_PARSER_HTML_FILTERING,
            )),
            new fieldCheckbox('parse_patterns', array(
                'title' => LANG_PARSER_PARSE_PATTERNS,
                'hint' => LANG_PARSER_PARSE_PATTERNS_HINT
            )),
            new fieldCheckbox('build_redirect_link', array(
                'title' => LANG_PARSER_BUILD_REDIRECT_LINK,
                'is_visible' => cmsController::enabled('redirect')
            )),
            new fieldNumber('teaser_len', array(
                'title' => LANG_PARSER_HTML_TEASER_LEN,
                'hint' => LANG_PARSER_HTML_TEASER_LEN_HINT,
            )),
            new fieldCheckbox('in_fulltext_search', array(
                'title' => LANG_PARSER_IN_FULLTEXT_SEARCH,
                'hint'  => LANG_PARSER_IN_FULLTEXT_SEARCH_HINT,
                'default' => false
            ))
        );
    }

    public function getFilterInput($value) {
        return ($this->show_filter_input_title ? '<label for="'.$this->id.'">'.$this->title.'</label>' : '') . html_input('text', $this->name, $value);
    }

    public function parse($value){

        if ($this->getOption('is_html_filter')){
            $value = cmsEventsManager::hook('html_filter', array(
                'text'                => $value,
                'is_auto_br'          => $this->getOption('editor') == 'markitup',
                'build_smiles'        => $this->getOption('editor') == 'markitup', // пока что только так
                'build_redirect_link' => (bool)$this->getOption('build_redirect_link')
            ));
        }

        if ($this->getOption('parse_patterns') && !empty($this->item)){
            $value = string_replace_keys_values_extended($value, $this->item);
        }

        return $value;

    }

    public function parseTeaser($value) {

        $max_len = $this->getOption('teaser_len');

        if ($max_len){

            $value = string_short($value, $max_len);

            if(!empty($this->item['ctype']['name']) && !empty($this->item['slug'])){
                $value .= '<a class="read-more" href="'.href_to($this->item['ctype']['name'], $this->item['slug'].'.html').'">'.LANG_MORE.'</a>';
            }

        } else if ($this->getOption('is_html_filter')){
            $value = cmsEventsManager::hook('html_filter', array(
                'text'                => $value,
                'is_auto_br'          => false,
                'build_smiles'        => $this->getOption('editor') == 'markitup', // пока что только так
                'build_redirect_link' => (bool)$this->getOption('build_redirect_link')
            ));
        } else if ($this->getOption('parse_patterns') && !empty($this->item)){
            $value = string_replace_keys_values_extended($value, $this->item);
        }

        return $value;

    }

    public function applyFilter($model, $value) {
        return $model->filterLike($this->name, "%{$value}%");
    }

    public function afterStore($item, $model, $action){

        if($action == 'add' && !empty($item[$this->name])){

            $paths = $this->getImagesPath($item[$this->name]);

            if($paths){
                foreach($paths as $path){

                    $model->filterEqual('path', $path)->filterIsNull('target_id');
                    $model->updateFiltered('uploaded_files', array('target_id' => $item['id']), true);

                }
            }

        }

        return;

    }

    public function delete($value){

        $paths = $this->getImagesPath($value);

        if($paths){

            $files_model = cmsCore::getModel('files');

            foreach($paths as $path){

                $file = $files_model->getFileByPath($path);
                if(!$file){ continue; }

                @unlink(cmsConfig::get('upload_path').$file['path']);

                $files_model->filterEqual('path', $file['path']);

                $files_model->deleteFiltered('uploaded_files');

            }

        }

        return true;

    }

    private function getImagesPath($text) {

        $upload_root = cmsConfig::get('upload_root');

        $matches = $paths = array();

        preg_match_all('#<img src="([^"]+)"#uis', $text, $matches, PREG_SET_ORDER);

        if($matches){
            foreach($matches as $match){

                if(empty($match[1])){ continue; }
                if(strpos($match[1], 'http') === 0){ continue; }

                $path = $match[1];

                if(strpos($path, $upload_root) === 0){
                    $path = str_replace($upload_root, '', $path);
                }

                $paths[] = $path;

            }
        }

        return $paths;

    }

    public function getInput($value){

        $this->data = cmsCore::getController('wysiwygs')->getEditorParams([
            'editor'  => $this->getOption('editor'),
            'options' => $this->getOption('editor_options', []),
            'presets' => $this->getOption('editor_presets', [])
        ]);

        return parent::getInput($value);

    }

}
