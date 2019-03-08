<?php

class onCommentsContentBeforeItem extends cmsAction {

    public function run($data){

        list($ctype, $item, $fields) = $data;

        if ($ctype['is_comments'] && $item['is_approved'] && !empty($item['is_comments_on'])){

            $this->target_controller = 'content';
            $this->target_subject    = $ctype['name'];
            $this->target_id         = $item['id'];
            $this->target_user_id    = $item['user_id'];

            $item['comments_widget'] = $this->getWidget();

        }

        return array($ctype, $item, $fields);

    }

}
