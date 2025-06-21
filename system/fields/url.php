<?php

class fieldUrl extends cmsFormField {

    public $title       = LANG_PARSER_URL;
    public $sql         = 'varchar({max_length}) NULL DEFAULT NULL';
    public $filter_type = 'str';
    public $allow_index = false;
    public $var_type    = 'string';

    public function getOptions() {
        return [
            new fieldCheckbox('redirect', [
                'title'      => LANG_PARSER_URL_REDIRECT,
                'default'    => false,
                'is_visible' => cmsController::enabled('redirect')
            ]),
            new fieldCheckbox('auto_http', [
                'title'   => LANG_PARSER_URL_AUTO_HTTP,
                'default' => true
            ]),
            new fieldNumber('max_length', [
                'title'   => LANG_PARSER_TEXT_MAX_LEN,
                'default' => 500
            ]),
            new fieldCheckbox('nofollow', [
                'title'   => LANG_PARSER_URL_NOFOLLOW,
                'default' => false
            ]),
            new fieldCheckbox('title', [
                'title'   => LANG_PARSER_URL_TITLE,
                'default' => false
            ]),
            new fieldString('css_class', [
                'title' => LANG_PARSER_URL_CSS_CLASS,
                'rules' => [
                    ['max_length', 100]
                ]
            ]),
            new fieldString('input_icon', [
                'title'  => defined('LANG_CP_ICON') ? LANG_CP_ICON : '',
                'suffix' => '<a href="#" class="icms-icon-select" data-href="' . href_to('admin', 'settings', ['theme', cmsConfig::get('http_template'), 'icon_list']) . '"><span>' . (defined('LANG_CP_ICON_SELECT') ? LANG_CP_ICON_SELECT : '') . '</span></a>'
            ]),
            new fieldCheckbox('only_input_icon', [
                'title'   => LANG_PARSER_URL_ONLY_ICON,
                'default' => false,
                'visible_depend' => ['options:input_icon' => ['hide' => ['']]]
            ])
        ];
    }

    public function getStringValue($value) {

        if (!$value || is_array($value)) {
            return '';
        }

        if ($this->getOption('title') && strpos($value, '|') !== false) {

            $result = explode('|', $value);

            if (!empty($result[1])) {
                $value = trim($result[1]);
            }
        }

        return $value;
    }

    public function parse($value) {

        $url_title = '';

        if (!$value) {
            return '';
        }

        if (!$this->getOption('title') && strpos($value, '|') === false) {

            $href = $value;

        } else {

            $result = explode('|', $value);

            $href = trim($result[0]);

            if (!empty($result[1])) {

                $value = trim($result[1]);

                $url_title = $value;
            }
        }

        if ($this->getOption('auto_http')) {
            if (!preg_match('/^([a-z]+):\/\/(.+)$/i', $href)) {
                $href = 'https://' . $href;
            }
        }

        if (!$url_title) {
            $url_title = parse_url($href, PHP_URL_HOST);
        }

        if ($this->getOption('redirect') && cmsController::enabled('redirect')) {
            $href = href_to('redirect') . '?url=' . urlencode($href);
        }

        $attr = [
            'rel'    => ['noopener'],
            'class'  => [],
            'target' => '_blank',
            'href'   => $href
        ];

        if ($this->getOption('nofollow')) {
            $attr['rel'][] = 'nofollow';
        }

        if ($this->getOption('css_class')) {
            $attr['class'][] = $this->getOption('css_class');
        }

        $link_text = '';

        $input_icon = $this->getOption('input_icon');

        if ($input_icon) {

            $icon_params = explode(':', $input_icon);
            if(!isset($icon_params[1])){ array_unshift($icon_params, 'solid'); }

            $link_text .= html_svg_icon($icon_params[0], $icon_params[1], 16, false);

            $attr['class'][] = 'has-icon';
        }

        if (!$this->getOption('only_input_icon') || !$input_icon) {
            $link_text .= html($url_title, false);
        }

        return '<a ' . html_attr_str($attr, false) . '>' . $link_text . '</a>';
    }

    public function applyFilter($model, $value) {
        return $model->filterLike($this->name, "%{$value}%");
    }

    public function store($value, $is_submitted, $old_value = null) {
        return strip_tags($value);
    }

    public function storeFilter($value) {
        return $this->store($value, false);
    }

}
