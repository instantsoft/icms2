<?php

class onSearchContentBeforeList extends cmsAction {

    public function run($data) {

        if (empty($this->options['is_hash_tag'])) {
            return $data;
        }

        list($ctype, $items) = $data;

        if (empty($items)) {
            return $data;
        }

        $fields = cmsCore::getModel('content')->filterIn('type', ['text', 'html'])->getContentFields($ctype['name']);
        if (!$fields) {
            return $data;
        }

        foreach ($items as $item) {
            foreach ($fields as $field) {

                if (!$field['is_in_list'] || empty($field['options']['in_fulltext_search'])) {
                    continue;
                }

                if (!empty($item[$field['name']])) {
                    $items[$item['id']][$field['name']] = $this->parseHashTag($item[$field['name']]);
                }
            }
        }

        return [$ctype, $items];
    }

}
