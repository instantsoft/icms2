<?php

class actionPhotosEdit extends cmsAction{

    public function run($photo_id = 0){

		if (!$photo_id) { cmsCore::error404(); }

        $photo = $this->model->getPhoto($photo_id);
        if (!$photo) { cmsCore::error404(); }

        $album = $this->model->getAlbum($photo['album_id']);
        if (!$album) { cmsCore::error404(); }

        $is_can_edit   = (cmsUser::isAllowed('albums', 'edit', 'all') ||
            (cmsUser::isAllowed('albums', 'edit', 'own') && $album['user_id'] == $this->cms_user->id) ||
            ($photo['user_id'] == $this->cms_user->id));

        if (!$is_can_edit) { cmsCore::error404(); }

        $ctype = $album['ctype']; unset($album['ctype']);

        $editor_params = cmsCore::getController('wysiwygs')->getEditorParams([
            'editor'  => $this->options['editor'],
            'presets' => $this->options['editor_presets']
        ]);

        if ($this->request->has('submit')){

            if (!$this->request->has('photos')) { $this->redirectBack(); }
            if (!$this->request->has('content')) { $this->redirectBack(); }

            // данные
            $photo_titles      = $this->request->get('photos', array());
            $photo_contents    = $this->request->get('content', array());
            $photo_is_privates = $this->request->get('is_private', array());
            $photo_types = array();
            if(!empty($this->options['types'])){
                $photo_types = $this->request->get('type', array());
            }

            if (!$photo_titles) { $this->redirectBack(); }

            // по ключам названий определяем id фотографий
            $_photo_ids = array_keys($photo_titles);
            // ключи могут быть только числовые
            $photo_ids = array_filter($_photo_ids, function ($v){
                return is_numeric($v);
            });

            if (!$photo_ids) { $this->redirectBack(); }

            // формируем массив для каждой фотографии
            $photo_list = array();

            foreach ($photo_ids as $photo_id) {

                // эти данные должны существовать, пусть даже и пустые
                // если их нет, значит запрос подделанный
                if(!isset($photo_titles[$photo_id]) ||
                        !isset($photo_contents[$photo_id])){
                    continue;
                }

                $_photo = array(
                    'title'      => strip_tags($photo_titles[$photo_id] ? $photo_titles[$photo_id] : sprintf(LANG_PHOTOS_PHOTO_UNTITLED, $photo_id)),
                    'content_source' => ($photo_contents[$photo_id] ? $photo_contents[$photo_id] : null),
                    'content'        => ($photo_contents[$photo_id] ? cmsEventsManager::hook('html_filter', [
                        'text'         => $photo_contents[$photo_id],
                        'is_auto_br'   => (!$editor_params['editor'] || $editor_params['editor'] == 'markitup'),
                        'build_smiles' => $editor_params['editor'] == 'markitup'
                    ]) : null),
                    'is_private' => (isset($photo_is_privates[$photo_id]) ? (int)$photo_is_privates[$photo_id] : 0),
                    'type'       => (isset($photo_types[$photo_id]) ? (int)$photo_types[$photo_id] : null)
                );

                $photo_list[$photo_id] = $_photo;

            }

            $this->model->updatePhotoList($photo_list);

            cmsUser::addSessionMessage(LANG_SUCCESS_MSG, 'success');

            $this->redirect(href_to('photos', $photo['slug'] . '.html'));

        }

        $this->cms_template->render('upload', array(
            'title'         => LANG_PHOTOS_EDIT_PHOTO,
            'editor_params' => $editor_params,
            'is_edit'       => true,
            'ctype'         => $ctype,
            'album'         => $album,
            'albums_select' => array(),
            'photos'        => array($photo),
            'preset_big'    => $this->options['preset'],
            'types'         => (!empty($this->options['types']) ? (array('' => LANG_PHOTOS_NO_TYPE) + $this->options['types']) : array())
        ));

    }

}
