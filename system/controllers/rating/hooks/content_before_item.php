<?php

class onRatingContentBeforeItem extends cmsAction {

    public function run($data){

        list($ctype, $item, $fields) = $data;

        if ($ctype['is_rating']){

            $this->setContext('content', $ctype['name']);

            $is_rating_allowed = cmsUser::isAllowed($ctype['name'], 'rate') && ($item['user_id'] != $this->cms_user->id);

            $item['rating_widget'] = $this->getWidget($item['id'], $item['rating'], $is_rating_allowed);

        }

        return array($ctype, $item, $fields);

    }

}
