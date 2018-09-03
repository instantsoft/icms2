<?php

class onTagsContentBeforeUpdate extends cmsAction {

    public function run($item){

        if (!empty($item['ctype_data']['is_tags'])){

            $item['tags'] = $this->model->updateTags($item['tags'], 'content', $item['ctype_data']['name'], $item['id']);

        }

        return $item;

    }

}
