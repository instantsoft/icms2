<?php

class onTagsContentAfterAdd extends cmsAction {

    public function run($item) {

        if (!empty($item['ctype_data']['is_tags'])) {

            $item['tags'] = $this->model->addTags($item['tags'], 'content', $item['ctype_data']['name'], $item['id']);

            cmsCore::getModel('content')->updateContentItemTags($item['ctype_data']['name'], $item['id'], $item['tags']);
        }

        return $item;
    }

}
