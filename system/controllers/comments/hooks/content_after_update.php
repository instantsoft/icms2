<?php

class onCommentsContentAfterUpdate extends cmsAction {

    public function run($item){

        $this->model->updateTracking('content', $item['ctype_data']['name'], $item['id']);

        // обновляем приватность комментариев
        if (isset($item['is_private'])){
            $this->model->filterCommentTarget('content', $item['ctype_data']['name'], $item['id'])->
                        updateCommentsPrivacy($item['is_private'] || $item['is_parent_hidden']);
        }

        // обновляем url
        if (!$item['ctype_data']['is_fixed_url']){

            $this->model->filterCommentTarget('content', $item['ctype_data']['name'], $item['id'])->
                        updateCommentsUrl(href_to_rel($item['ctype_data']['name'], $item['slug'].'.html'), $item['title']);

        }

        return $item;

    }

}
