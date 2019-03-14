<?php

class onRatingPhotosBeforeItem extends cmsAction {

    public function run($data){

        list($photo, $album, $ctype) = $data;

        if ($ctype['is_rating']){

            $this->setContext('photos', $ctype['name']);

            $is_rating_allowed = cmsUser::isAllowed($ctype['name'], 'rate') && ($photo['user_id'] != $this->cms_user->id);

            $photo['rating_widget'] = $this->getWidget($photo['id'], $photo['rating'], $is_rating_allowed);

        }

        return array($photo, $album, $ctype);

    }

}
