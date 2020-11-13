<?php

class onRatingPhotosBeforeItem extends cmsAction {

    public function run($data){

        list($photo, $album, $ctype) = $data;

        if ($ctype['is_rating']){

            $this->setContext('photos', $ctype['name']);

            $this->loadCurrentUserVoted([$photo['id']]);
            $this->loadCurrentTotalVoted($photo['id']);

            $is_rating_allowed = cmsUser::isAllowed($ctype['name'], 'rate') && ($photo['user_id'] != $this->cms_user->id);

            if(!empty($ctype['options']['rating_template'])){
                $this->setOption('template', $ctype['options']['rating_template']);
            }

            $photo['rating_widget'] = $this->getWidget($photo['id'], $photo['rating'], $is_rating_allowed);

        }

        return array($photo, $album, $ctype);

    }

}
