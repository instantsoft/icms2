<?php

class actionPhotosView extends cmsAction{

    public function run($id = false){

        if (!$id) { cmsCore::error404(); }

        $photo = $this->model->getPhoto($id);
        if (!$photo){ cmsCore::error404(); }

        $album = $this->model->getAlbum($photo['album_id']);
        if (!$album){ cmsCore::error404(); }

        $photos = $this->model->getPhotos($album['id']);

        $ctype = $album['ctype'];

        $template = cmsTemplate::getInstance();
        $user = cmsUser::getInstance();

        // Проверяем прохождение модерации
        $is_moderator = false;
        if (!$album['is_approved']){
            $is_moderator = $user->is_admin || cmsCore::getModel('content')->userIsContentTypeModerator($ctype['name'], $user->id);
            if (!$is_moderator && $user->id != $album['user_id']){ cmsCore::error404(); }
        }

        // Проверяем приватность
        if ($album['is_private']){
            $is_friend = $user->isFriend($album['user_id']);
            $is_can_view_private = cmsUser::isAllowed($ctype['name'], 'view_all');
            if (!$is_friend && !$is_can_view_private && !$is_moderator){ cmsCore::error404(); }
        }

        // Рейтинг
        if ($ctype['is_rating'] &&  $this->isControllerEnabled('rating')){

            $rating_controller = cmsCore::getController('rating', new cmsRequest(array(
                'target_controller' => $this->name,
                'target_subject' => 'photo'
            ), cmsRequest::CTX_INTERNAL));

            $is_rating_allowed = cmsUser::isAllowed($ctype['name'], 'rate') && ($photo['user_id'] != $user->id);

            $photo['rating_widget'] = $rating_controller->getWidget($photo['id'], $photo['rating'], $is_rating_allowed);

        }

        // Комментарии
        if ($ctype['is_comments'] && $this->isControllerEnabled('comments')){

            $comments_controller = cmsCore::getController('comments', new cmsRequest(array(
                'target_controller' => $this->name,
                'target_subject' => 'photo',
                'target_id' => $photo['id']
            ), cmsRequest::CTX_INTERNAL));

            $photo['comments_widget'] = $comments_controller->getWidget();

        }

        return $template->render('view', array(
            'photo' => $photo,
            'photos' => $photos,
            'album' => $album,
            'ctype' => $ctype,
			'is_origs' => !empty($this->options['is_origs'])
        ));

    }

}
