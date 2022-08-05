<?php

class onTagsContentBeforeList extends cmsAction {

    public function run($data) {

        list($ctype, $items) = $data;

        if ($ctype['is_tags'] && !empty($ctype['options']['is_tags_in_list']) && $items) {

            foreach ($items as $id => $item) {

                if ($item['tags']) {

                    $items[$id]['tags'] = explode(',', $item['tags']);

                    $items[$id]['show_tags'] = true;
                }
            }
        }

        return [$ctype, $items];
    }

}
