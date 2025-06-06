<?php

class fieldCategory extends cmsFormField {

    public $sql                  = '';
    public $is_virtual           = true;
    public $allow_index          = false;
    public $excluded_controllers = ['forms', 'users', 'groups'];
    public $filter_type = 'int';
    public $var_type = 'integer';

    private $link_options = null;

    protected $use_language = true;

    public function __construct($name, $options = false) {

        parent::__construct($name, $options);

        if(!$this->title){
            $this->title = LANG_F_CATEGORY;
        }
    }

    public function getOptions() {
        return [
            new fieldCheckbox('is_auto_colors', [
                'title'           => LANG_F_CATEGORY_IS_AUTO_COLORS,
                'extended_option' => true
            ]),
            new fieldString('auto_colors_classes', [
                'title'           => LANG_F_CATEGORY_AUTO_COLORS_CLASSES,
                'hint'            => LANG_F_CATEGORY_AUTO_COLORS_CLASSES_HINT,
                'default'         => 'btn-primary,btn-secondary,btn-success,btn-danger,btn-warning,btn-info,btn-light,btn-dark',
                'visible_depend'  => ['options:is_auto_colors' => ['show' => ['1']]],
                'extended_option' => true
            ]),
            new fieldString('btn_class', [
                'title'           => LANG_F_CATEGORY_BTN_CLASS,
                'default'         => 'btn btn-sm',
                'extended_option' => true
            ]),
            new fieldString('btn_icon', [
                'title'  => LANG_F_CATEGORY_BTN_ICON,
                'suffix' => '<a href="#" class="icms-icon-select" data-href="' . href_to('admin', 'settings', ['theme', cmsConfig::get('http_template'), 'icon_list']) . '"><span>' . (defined('LANG_CP_ICON_SELECT') ? LANG_CP_ICON_SELECT : '') . '</span></a>'
            ]),
            new fieldCheckbox('filter_multiple', [
                'title'   => LANG_PARSER_LIST_FILTER_MULTI,
                'default' => false
            ])
        ];
    }

    public function getDefaultVarType($is_filter = false) {

        if ($this->context === 'filter') {
            $is_filter = true;
        }

        if ($is_filter && $this->getOption('filter_multiple')) {
            $this->var_type = 'array';
        }

        return parent::getDefaultVarType($is_filter);
    }

    public function getStringValue($value) {
        return '';
    }

    public function parse($value) {

        if (empty($this->item['category']['id']) || empty($this->item['ctype_name'])) {
            return '';
        }

        $ctype_default = cmsConfig::get('ctype_default');

        $base_url = ($ctype_default && in_array($this->item['ctype_name'], $ctype_default)) ? '' : $this->item['ctype_name'];

        $cats = [];

        if (empty($this->item['category']['is_hidden'])) {
            $cats[$this->item['category']['id']] = [
                'title' => $this->item['category']['title'],
                'href'  => href_to($base_url, $this->item['category']['slug'])
            ];
        }

        if (!empty($this->item['categories'])) {
            foreach ($this->item['categories'] as $category) {
                if (empty($category['is_hidden'])) {
                    $cats[$category['id']] = [
                        'title' => $category['title'],
                        'href'  => href_to($base_url, $category['slug'])
                    ];
                }
            }
        }

        list($btn_class, $btn_icon, $auto_colors_class_list) = $this->getLinkOptions();

        if ($auto_colors_class_list) {

            $auto_class_count = count($auto_colors_class_list);

            array_unshift($auto_colors_class_list, '', '');
        }

        $html = '';

        foreach ($cats as $cat_id => $cat) {

            $_btn_class = $btn_class;
            if ($auto_colors_class_list) {

                if (isset($auto_colors_class_list[$cat_id])) {
                    $_btn_class[] = $auto_colors_class_list[$cat_id];
                } else {
                    $key          = $this->item['category']['id'] % $auto_class_count;
                    $_btn_class[] = $auto_colors_class_list[$key];
                }
            }

            $html .= '<a class="' . implode(' ', $_btn_class) . '" href="' . $cat['href'] . '">' . $btn_icon . ' ' . $cat['title'] . '</a> ';
        }

        return $html;
    }

    public function applyFilter($model, $value) {

        if (empty($this->item['ctype_name'])) {
            return $model;
        }

        $content = cmsCore::getModel('content');

        $ctype = $content->getContentTypeByName($this->item['ctype_name']);

        $table_name      = $model->getContentCategoryTableName($ctype['name']);
        $bind_table_name = $table_name . '_bind';

        if (!$model->isJoined($bind_table_name, 'b')) {
            $model->joinInner($bind_table_name, 'b', 'b.item_id = i.id');
        }

        if (is_array($value)) {

            $model->filterIn('b.category_id', $value);

        } else {

            if ($ctype['is_cats_recursive']) {

                $category = $content->getCategory($ctype['name'], $value);

                if (!$category) {
                    return $model->filter('1 = 0');
                }

                if (!empty($ctype['options']['is_cats_multi'])) {
                    $model->distinctSelect();
                }

                if (!$model->isJoined($table_name, 'c')) {
                    $model->joinInner($table_name, 'c', 'c.id = b.category_id');
                }

                $model->filterGtEqual('c.ns_left', $category['ns_left']);
                $model->filterLtEqual('c.ns_right', $category['ns_right']);

            } else {

                $model->filterEqual('b.category_id', $value);
            }
        }

        return $model;
    }

    public function getFilterInput($value){

        if (empty($this->item_list['ctype'])) {
            return '';
        }

        if (!empty($this->options['filter_multiple'])) {
            if(!is_array($value)){
                $value = [];
            }
        }

        if (!$this->show_filter_input_title) {
            $this->title = false;
        }

        $category_id = $this->item_list['category_id'] ?: 1;

        $tree = cmsCore::getModel('content')->getSubCategoriesTree($this->item_list['ctype']['name'], $category_id, 0, false) ?: [];

        if (!$tree) {
            return '';
        }

        $this->data['items'] = ['' => ''];

        foreach ($tree as $c) {

            $level = ($category_id > 1 ? $c['ns_level']-2 : $c['ns_level']-1);

            $this->data['items'][$c['id']] = str_repeat('-- ', $level) . $c['title'];
        }

        $this->data['dom_attr'] = ['id' => $this->id];

        if (!empty($this->options['filter_multiple'])) {
            $this->data['dom_attr']['multiple'] = true;
        }

        return cmsTemplate::getInstance()->renderFormField($this->class . '_filter', [
            'field' => $this,
            'value' => $value
        ]);
    }

    public function getInput($value) {
        return '';
    }

    public function getLinkOptions() {

        if ($this->link_options === null) {

            $btn_icon = $this->getOption('btn_icon', '');

            if ($btn_icon) {
                $icon_params = explode(':', $btn_icon);
                if (!isset($icon_params[1])) {
                    array_unshift($icon_params, 'solid');
                }
                $btn_icon = html_svg_icon($icon_params[0], $icon_params[1], 16, false);
            }

            $auto_colors_class_list = [];

            $btn_class = [$this->getOption('btn_class', '')];

            if ($this->getOption('is_auto_colors')) {

                $auto_colors_classes = $this->getOption('auto_colors_classes', '');

                if ($auto_colors_classes) {

                    $auto_colors_class_list = explode(',', $auto_colors_classes);
                }
            }

            $this->link_options = [$btn_class, $btn_icon, $auto_colors_class_list];
        }

        return $this->link_options;
    }

}
