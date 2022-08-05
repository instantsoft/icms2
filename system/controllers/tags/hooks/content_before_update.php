<?php

class onTagsContentBeforeUpdate extends cmsAction {

    public function run($item) {

        if (!empty($item['ctype_data']['is_tags']) && array_key_exists('tags', $item)) {

            $item['tags'] = $this->model->updateTags($item['tags'], 'content', $item['ctype_data']['name'], $item['id']);
        }

        return $item;
    }

}
