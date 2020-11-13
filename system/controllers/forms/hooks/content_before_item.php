<?php

class onFormsContentBeforeItem extends cmsAction {

    public function run($data) {

        if (empty($this->options['allow_shortcode'])) {
            return $data;
        }

        list($ctype, $item, $fields) = $data;

        foreach ($fields as $field) {

            if (!in_array($field['type'], array('text', 'html'))) {
                continue;
            }
            if (!$field['is_in_item']) {
                continue;
            }

            if (!empty($item[$field['name']])) {
                $fields[$field['name']]['html'] = $this->parseShortcode($field['html']);
            }
        }

        return [$ctype, $item, $fields];
    }

}
