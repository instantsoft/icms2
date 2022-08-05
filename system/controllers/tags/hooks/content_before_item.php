<?php

class onTagsContentBeforeItem extends cmsAction {

    public function run($data) {

        list($ctype, $item, $fields) = $data;

        if ($ctype['is_tags'] && !empty($ctype['options']['is_tags_in_item']) && $item['tags']) {

            $item['tags'] = explode(',', $item['tags']);

            // в случае выключения контроллера
            // по этому флагу определяем включенность
            $item['show_tags'] = true;
        }

        return [$ctype, $item, $fields];
    }

}
