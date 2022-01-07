<?php

class fieldToolbar extends cmsFormField {

    public $title       = LANG_PARSER_TOOLBAR;
    public $sql         = '';
    public $is_virtual  = true;
    public $allow_index = false;

    public function getOptions() {

        if(!$this->subject_name){
            return [];
        }

        return [
            new fieldList('fields_list', [
                'title'        => LANG_PARSER_TOOLBAR_FL_TITLE,
                'hint'         => LANG_PARSER_TOOLBAR_FL_HINT,
                'is_multiple'  => true,
                'dynamic_list' => true,
                'select_title' => LANG_PARSER_TOOLBAR_FL_SEL,
                'multiple_keys' => [
                    'name' => 'field', 'icon' => 'field_value'
                ],
                'rules' => [
                    ['required']
                ],
                'generator' => function(){
                    return $this->getAvailableFormFields();
                }
            ])
        ];
    }

    private function getAvailableFormFields() {

        $content_model = cmsCore::getModel('content');

        if (!$content_model->getContentTypeByName($this->subject_name)) {
            $content_model->setTablePrefix('');
        }

        $fields = $content_model->orderBy('ordering', 'asc')->
                getContentFields($this->subject_name, false, false);


        $fields_types = [];

        foreach ($fields as $field) {

            if (in_array($field['type'], ['toolbar', 'captcha', 'users',
                'caption', 'forms', 'hidden', 'image',
                'html', 'images', 'parent', 'text'])) {
                continue;
            }

            if ($field['handler']->is_virtual || strpos($field['handler']->sql, 'text') !== false) {
                continue;
            }

            $fields_types[$field['name']] = $field['title'];
        }

        return $fields_types;
    }

    public function getStringValue($value) {
        return '';
    }

    public function parse($value) {
        return '1'; // Чтобы потом вызвался hookItem
    }

    public function hookItem($item, $fields){

        $toolbar = [];

        $fields_list = $this->getOption('fields_list', []);

        foreach ($fields_list as $field_data) {

            // Есть ли в инфобаре это поле?
            if(isset($item['info_bar'][$field_data['name']])){

                $toolbar[$field_data['name']] = [
                    'icon'  => $item['info_bar'][$field_data['name']]['icon'],
                    'value' => '',
                    'href'  => !empty($item['info_bar'][$field_data['name']]['href']) ? $item['info_bar'][$field_data['name']]['href'] : '',
                    'html'  => $item['info_bar'][$field_data['name']]['html']
                ];

                unset($item['info_bar'][$field_data['name']]);

                // Для показа папок костыль
                if($field_data['name'] === 'user' && isset($item['info_bar']['folder'])){

                    $toolbar['folder'] = [
                        'icon'  => $item['info_bar']['folder']['icon'],
                        'value' => '',
                        'href'  => $item['info_bar']['folder']['href'],
                        'html'  => $item['info_bar']['folder']['html']
                    ];

                    unset($item['info_bar']['folder']);
                }
            }

            // Есть ли в полях для показа
            if(isset($item['fields'][$field_data['name']])){

                $toolbar[$field_data['name']] = [
                    'icon'  => $field_data['icon'],
                    'href'  => '',
                    'value' => isset($item[$field_data['name']]) ? $item[$field_data['name']] : '',
                    'html'  => $item['fields'][$field_data['name']]['html']
                ];

                unset($item['fields'][$field_data['name']]);
            }

        }

        $toolbar_html = '';

        if ($toolbar){
            ob_start(); ?>
            <div class="icms-content-toolbar">
                <?php foreach($toolbar as $bar){ ?>
                    <?php if (!empty($bar['icon'])) {
                        $icon_params = explode(':', $bar['icon']);
                        if(!isset($icon_params[1])){ array_unshift($icon_params, 'solid'); }
                    } ?>
                    <div class="icms-content-toolbar__item<?php echo !empty($icon_params[2]) ? ' '.$icon_params[2].($bar['value'] ? ' '.strstr($icon_params[2], ' ', true).'-'.$bar['value'] : '') : ''; ?>">
                        <?php if (!empty($icon_params[1])){ ?>
                            <?php html_svg_icon($icon_params[0], $icon_params[1]); ?>
                        <?php } ?>
                        <?php if (!empty($bar['href'])){ ?>
                            <a href="<?php echo $bar['href']; ?>"><?php echo $bar['html']; ?></a>
                        <?php } else { ?>
                            <?php echo $bar['html']; ?>
                        <?php } ?>
                    </div>
                <?php } ?>
            </div>
        <?php
            $toolbar_html = ob_get_clean();
        }

        $item['fields'][$this->name]['html'] = $toolbar_html;

        return $item;
    }

    public function getInput($value) {
        return '';
    }

}
