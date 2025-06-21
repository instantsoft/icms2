<?php

class fieldCheckbox extends cmsFormField {

    public $title       = LANG_PARSER_CHECKBOX;
    public $sql         = 'TINYINT(1) UNSIGNED NULL DEFAULT NULL';
    public $filter_type = 'int';
    public $var_type    = 'integer';

    public function getOptions(){
        return [
            new fieldFieldsgroup('urls', [
                'title'            => LANG_PARSER_CHECKBOX_LINKS,
                'hint'             => LANG_PARSER_CHECKBOX_LINKS_HINT,
                'add_title'        => LANG_PARSER_CHECKBOX_LINKS_ADD,
                'is_counter_list'  => true,
                'childs' => [
                    new fieldString('title', [
                        'title' => LANG_TITLE,
                        'rules' => [['required']]
                    ]),
                    new fieldString('href', [
                        'title' => LANG_SLUG,
                        'hint'  => LANG_PARSER_CHECKBOX_LINKS_SLASH,
                        'rules' => [['required']]
                    ]),
                    new fieldString('class', [
                        'title' => LANG_PARSER_URL_CSS_CLASS
                    ])
                ]
            ])
        ];
    }

    public function getTitle() {
        return $this->element_title ? string_replace_keys_values($this->element_title, $this->getTitleLinks()) : $this->title;
    }

    public function parse($value) {
        return $value ? LANG_YES : LANG_NO;
    }

    public function applyFilter($model, $value) {
        return $model->filterEqual($this->name, 1);
    }

    public function getInput($value) {

        $this->element_title = string_replace_keys_values($this->element_title, $this->getTitleLinks());

        $this->data['attributes']             = $this->getProperty('attributes') ?: [];
        $this->data['attributes']['id']       = $this->id;
        $this->data['attributes']['required'] = (array_search(['required'], $this->getRules()) !== false);

        if (empty($this->data['attributes']['class'])) {
            $this->data['attributes']['class'] = 'custom-control-input';
        } else {
            $this->data['attributes']['class'] .= ' custom-control-input';
        }

        return parent::getInput($value);
    }

    private function getTitleLinks() {

        $links = [];

        $urls = $this->getOption('urls', []);

        foreach ($urls as $key => $link) {

            if (stripos($link['href'], 'http') !== 0) {
                $link['href'] = rel_to_href($link['href']);
            }

            $css_class = '';
            if (!empty($link['class'])) {
                $css_class = ' class="' . html($link['class'], false) . '"';
            }

            $links['link' . ($key+1)] = '<a'.$css_class.' href="'.html($link['href'], false).'" target="_blank">'.html($link['title'], false).'</a>';
        }

        return $links;
    }

}
