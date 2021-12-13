<?php

class onSearchContentBeforeItem extends cmsAction {

    public function run($data) {

        if (empty($this->options['is_hash_tag'])) {
            return $data;
        }

        list($ctype, $item, $fields) = $data;

        foreach ($fields as $field) {

            if (!in_array($field['type'], ['text', 'html']) || empty($field['options']['in_fulltext_search'])) {
                continue;
            }

            if (!empty($item[$field['name']])) {
                $fields[$field['name']]['html'] = $this->parseHashTag($field['html']);
            }
        }

        return [$ctype, $item, $fields];
    }

}
