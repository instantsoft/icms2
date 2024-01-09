<?php

class fieldHtml extends cmsFormField {

    public $title       = LANG_PARSER_HTML;
    public $sql         = 'mediumtext';
    public $filter_type = 'str';
    public $allow_index = false;
    public $var_type    = 'string';

    public function getOptions(){
        return [
            new fieldList('editor', [
                'title'     => LANG_PARSER_HTML_EDITOR,
                'default'   => cmsConfig::get('default_editor'),
                'generator' => function ($item) {
                    $items   = [];
                    $editors = cmsCore::getWysiwygs();
                    foreach ($editors as $editor) {
                        $items[$editor] = ucfirst($editor);
                    }
                    $ps = cmsCore::getModel('wysiwygs')->getPresetsList();
                    if ($ps) {
                        foreach ($ps as $key => $value) {
                            $items[$key] = $value;
                        }
                    }
                    return $items;
                }
            ]),
            new fieldList('editor_presets', [
                'title'         => LANG_PARSER_HTML_EDITOR_GR,
                'is_multiple'   => true,
                'dynamic_list'  => true,
                'select_title'  => LANG_SELECT,
                'multiple_keys' => [
                    'group_id'  => 'field', 'preset_id' => 'field_select'
                ],
                'generator'     => function ($item) {
                    $users_model = cmsCore::getModel('users');

                    $items = [];

                    $groups = $users_model->getGroups(false);

                    foreach ($groups as $group) {
                        $items[$group['id']] = $group['title'];
                    }

                    return $items;
                },
                'values_generator' => function () {
                    $items   = [];
                    $editors = cmsCore::getWysiwygs();
                    foreach ($editors as $editor) {
                        $items[$editor] = ucfirst($editor);
                    }
                    $ps = cmsCore::getModel('wysiwygs')->getPresetsList();
                    if ($ps) {
                        foreach ($ps as $key => $value) {
                            $items[$key] = $value;
                        }
                    }
                    return $items;
                }
            ]),
            new fieldCheckbox('is_html_filter', [
                'title'           => LANG_PARSER_HTML_FILTERING,
                'extended_option' => true
            ]),
            new fieldList('typograph_id', [
                'title'     => LANG_PARSER_TYPOGRAPH,
                'generator' => function ($item) {
                    $items   = [];
                    $presets = (new cmsModel())->get('typograph_presets') ?: [];
                    foreach ($presets as $preset) {
                        $items[$preset['id']] = $preset['title'];
                    }
                    return $items;
                },
                'rules' => [
                    ['required']
                ]
            ]),
            new fieldCheckbox('parse_patterns', [
                'title' => LANG_PARSER_PARSE_PATTERNS,
                'hint'  => LANG_PARSER_PARSE_PATTERNS_HINT
            ]),
            new fieldCheckbox('build_redirect_link', [
                'title'      => LANG_PARSER_BUILD_REDIRECT_LINK,
                'is_visible' => cmsController::enabled('redirect')
            ]),
            new fieldNumber('teaser_len', [
                'title'           => LANG_PARSER_HTML_TEASER_LEN,
                'hint'            => LANG_PARSER_HTML_TEASER_LEN_HINT,
                'extended_option' => true
            ]),
            new fieldString('teaser_postfix', [
                'title'           => LANG_PARSER_HTML_TEASER_POSTFIX,
                'visible_depend'  => ['options:teaser_len' => ['hide' => ['']]],
                'default'         => '',
                'extended_option' => true
            ]),
            new fieldList('teaser_type', [
                'title'           => LANG_PARSER_HTML_TEASER_TYPE,
                'items'           => [
                    ''  => LANG_PARSER_HTML_TEASER_TYPE_NULL,
                    's' => LANG_PARSER_HTML_TEASER_TYPE_S,
                    'w' => LANG_PARSER_HTML_TEASER_TYPE_W
                ],
                'visible_depend'  => ['options:teaser_len' => ['hide' => ['']]],
                'extended_option' => true
            ]),
            new fieldCheckbox('show_show_more', [
                'title'           => LANG_PARSER_SHOW_SHOW_MORE,
                'default'         => false,
                'visible_depend'  => ['options:teaser_len' => ['hide' => ['']]],
                'extended_option' => true
            ]),
            new fieldCheckbox('in_fulltext_search', [
                'title'   => LANG_PARSER_IN_FULLTEXT_SEARCH,
                'hint'    => LANG_PARSER_IN_FULLTEXT_SEARCH_HINT,
                'default' => false
            ])
        ];
    }

    public function getFilterInput($value) {
        return ($this->show_filter_input_title ? '<label for="'.$this->id.'">'.$this->title.'</label>' : '') . html_input('text', $this->name, $value);
    }

    public function getStringValue($value){

        if (is_empty_value($value)) {
            return '';
        }

        if ($this->getOption('parse_patterns') && !empty($this->item)){
            $value = string_replace_keys_values_extended($value, $this->item);
        }

        return trim(strip_tags($value));
    }

    public function afterParse($value, $item){

        if (is_empty_value($value)) {
            return '';
        }

        if ($this->getOption('parse_patterns')){
            $value = string_replace_keys_values_extended($value, $item);
        }

        return $value;
    }

    public function parse($value){

        if (is_empty_value($value)) {
            return '';
        }

        if ($this->getOption('is_html_filter')){
            $value = cmsEventsManager::hook('html_filter', [
                'text'                => $value,
                'typograph_id'        => $this->getOption('typograph_id'),
                // Эта опция есть в пресете, перезапишет
                'build_redirect_link' => (bool)$this->getOption('build_redirect_link')
            ]);
            $value = string_replace_svg_icons($value);
        }

        return $value;
    }

    public function parseTeaser($value) {

        if (is_empty_value($value)) {
            return '';
        }

        if (!empty($this->item['is_private_item'])) {
            return '<p class="private_field_hint text-muted">'.$this->item['private_item_hint'].'</p>';
        }

        $max_len = $this->getOption('teaser_len', 0);

        if ($max_len){

            $teaser_postfix = $this->getOption('teaser_postfix', '');
            $teaser_type = $this->getOption('teaser_type', 's');

            $value = string_short($value, $max_len, $teaser_postfix, $teaser_type);

            if($this->getOption('show_show_more') && !empty($this->item['ctype']['name']) && !empty($this->item['slug'])){
                $value .= '<span class="d-block mt-2"><a class="read-more btn btn-outline-info btn-sm" href="'.href_to($this->item['ctype']['name'], $this->item['slug'].'.html').'">'.LANG_MORE.'</a></span>';
            }

        } else if ($this->getOption('is_html_filter')){
            $value = cmsEventsManager::hook('html_filter', [
                'text'                => $value,
                'typograph_id'        => $this->getOption('typograph_id'),
                'build_redirect_link' => (bool)$this->getOption('build_redirect_link')
            ]);
        }

        if ($this->getOption('parse_patterns') && !empty($this->item)){
            $value = string_replace_keys_values_extended($value, $this->item);
        }

        return $value;
    }

    public function applyFilter($model, $value) {
        return $model->filterLike($this->name, "%{$value}%");
    }

    public function store($value, $is_submitted, $old_value = null) {

        // Сохраняем через типограф если поле в типе контента или передана опция
        if($this->getProperty('store_via_html_filter') || ($this->getOption('is_html_filter') && $this->field_id)){

            $value = cmsEventsManager::hook('html_filter', [
                'text'                => $value,
                // Отключаем обработку колбэков
                'is_process_callback' => false,
                'typograph_id'        => $this->getOption('typograph_id')
            ]);
        }

        return $value;
    }

    public function afterStore($item, $model, $action) {

        if ($action === 'add' && !empty($item[$this->name])) {

            $paths = string_html_get_images_path($item[$this->name]);

            if ($paths) {
                foreach ($paths as $path) {

                    $model->filterEqual('path', $path)->filterIsNull('target_id');
                    $model->updateFiltered('uploaded_files', ['target_id' => $item['id']], true);
                }
            }
        }

        return;
    }

    public function delete($value) {

        if (is_empty_value($value) || empty($this->item['id'])) {
            return true;
        }

        $paths = string_html_get_images_path($value);

        if ($paths) {

            $files_model = cmsCore::getModel('files');

            foreach ($paths as $path) {

                $file = $files_model->getFileByPath($path);
                // Нет файла или он от другой записи
                if (!$file || $this->item['id'] != $file['target_id']) {
                    continue;
                }

                $files_model->deleteFile($file);
            }
        }

        return true;
    }

    public function getInput($value) {

        $this->data = cmsCore::getController('wysiwygs')->getEditorParams([
            'editor'  => $this->getOption('editor'),
            'options' => $this->getOption('editor_options', []),
            'presets' => $this->getOption('editor_presets', [])
        ]);

        if (empty($this->data['options']['id'])) {
            $this->data['options']['id'] = $this->id;
        }

        $this->id = $this->data['options']['id'];

        return parent::getInput($value);
    }

}
