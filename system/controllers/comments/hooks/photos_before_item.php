<?php

class onCommentsPhotosBeforeItem extends cmsAction {

    public function run($data){

        list($photo, $album, $ctype) = $data;

        if ($ctype['is_comments']){

            $this->target_controller = 'photos';
            $this->target_subject    = 'photo';
            $this->target_id         = $photo['id'];
            $this->target_user_id    = $photo['user_id'];

            $photo['comments_widget'] = $this->getWidget();

        }

        return array($photo, $album, $ctype);

    }

}
