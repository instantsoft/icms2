<?php

class actionTagsAutocomplete extends cmsAction {

    public function run(){

        if (!$this->request->isAjax()) { cmsCore::error404(); }

        $term = $this->request->get('term');

        if (!$term) { cmsCore::error404(); }

        $tags = $this->model->filterLike("tag", "{$term}%")->getTags();

        $result = array();

        if ($tags){
            foreach($tags as $tag){
                $result[] = array(
                    'id' => $tag['id'],
                    'label' => $tag['tag'],
                    'value' => $tag['tag']
                );
            }
        }

        cmsTemplate::getInstance()->renderJSON($result);

    }

}
