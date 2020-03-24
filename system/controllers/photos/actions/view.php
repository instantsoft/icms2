<?php

class actionPhotosView extends cmsAction {

    private $related_title = '';

    public function __construct($controller, $params=array()) {

        parent::__construct($controller, $params);

        cmsCore::loadControllerLanguage('content');

    }

    public function run($id = false){

        // редиректим со старых адресов
        if ($id) {

            $photo = $this->model->getPhoto($id);
            if (!$photo || !$photo['slug']) { cmsCore::error404(); }

            $this->redirect(href_to('photos', $photo['slug'] . '.html'), 301);

        }

        $slug = $this->request->get('slug', '');
        if (!$slug) { cmsCore::error404(); }

        $photo = $this->model->getPhoto($slug);
        if (!$photo) { cmsCore::error404(); }

        $album = $this->model->getAlbum($photo['album_id']);
        if (!$album) { cmsCore::error404(); }

        $ctype = $album['ctype']; unset($album['ctype']);

        list($photo, $album, $ctype) = cmsEventsManager::hook('photos_before_item', array($photo, $album, $ctype));

        // Проверяем прохождение модерации
        $is_moderator = $this->cms_user->is_admin;
        if(!$is_moderator && $this->cms_user->is_logged){
            $is_moderator = cmsCore::getModel('moderation')->userIsContentModerator($ctype['name'], $this->cms_user->id);
        }

        // на модерации
        if (!$album['is_approved']){

            $item_view_notice = $album['is_draft'] ? LANG_CONTENT_DRAFT_NOTICE : LANG_MODERATION_NOTICE;

            if (!$is_moderator && $this->cms_user->id != $album['user_id']){
                return cmsCore::errorForbidden($item_view_notice, true);
            }

            cmsUser::addSessionMessage($item_view_notice, 'info');

        }

        // Проверяем приватность альбома
        if ($album['is_private'] == 1){ // доступ только друзьям

            $is_friend           = $this->cms_user->isFriend($album['user_id']);
            $is_can_view_private = cmsUser::isAllowed($ctype['name'], 'view_all');

            if (!$is_friend && !$is_can_view_private && !$is_moderator){
                // если в настройках указано скрывать, 404
                if(empty($ctype['options']['privacy_type']) || $ctype['options']['privacy_type'] == 'hide'){
                    cmsCore::error404();
                }
                // иначе пишем, к кому в друзья нужно проситься
                cmsUser::addSessionMessage(sprintf(
                    LANG_CONTENT_PRIVATE_FRIEND_INFO,
                    (!empty($ctype['labels']['one']) ? $ctype['labels']['one'] : LANG_PAGE),
                    href_to('users', $album['user_id']),
                    htmlspecialchars($album['user']['nickname'])
                ), 'info');
                $this->redirect(href_to($ctype['name']));
            }

        }

        // Проверяем приватность фото
        if ($photo['is_private'] == 1){ // доступ только друзьям

            $is_friend           = $this->cms_user->isFriend($photo['user_id']);
            $is_can_view_private = cmsUser::isAllowed($ctype['name'], 'view_all');

            if (!$is_friend && !$is_can_view_private && !$is_moderator){
                // иначе пишем, к кому в друзья нужно проситься
                cmsUser::addSessionMessage(sprintf(
                    LANG_CONTENT_PRIVATE_FRIEND_INFO,
                    LANG_PHOTOS_WP_ITEM,
                    href_to('users', $photo['user_id']),
                    htmlspecialchars($photo['user']['nickname'])
                ), 'info');
                $this->redirect(href_to($ctype['name'], $album['slug'] . '.html'));
            }

        }

        // Проверяем ограничения доступа из других контроллеров
        if ($album['is_parent_hidden'] || $album['is_private']){
            $is_parent_viewable_result = cmsEventsManager::hook('content_view_hidden', array(
                'viewable'     => true,
                'item'         => $album,
                'is_moderator' => $is_moderator,
                'ctype'        => $ctype
            ));
            if (!$is_parent_viewable_result['viewable']){

                if(isset($is_parent_viewable_result['access_text'])){

                    cmsUser::addSessionMessage($is_parent_viewable_result['access_text'], 'error');

                    if(isset($is_parent_viewable_result['access_redirect_url'])){
                        $this->redirect($is_parent_viewable_result['access_redirect_url']);
                    } else {
                        $this->redirect(href_to($ctype['name']));
                    }

                }

                cmsUser::goLogin();
            }
        }

		if ($this->cms_user->id != $photo['user_id']){
			$this->model->incrementCounter($photo['id']);
		}

        $is_can_set_cover = (cmsUser::isAllowed($ctype['name'], 'edit', 'all') ||
                (cmsUser::isAllowed($ctype['name'], 'edit', 'own') && $album['user_id'] == $this->cms_user->id));
        $is_can_edit   = (cmsUser::isAllowed($ctype['name'], 'edit', 'all') ||
                (cmsUser::isAllowed($ctype['name'], 'edit', 'own') && $album['user_id'] == $this->cms_user->id) ||
                ($photo['user_id'] == $this->cms_user->id));
        $is_can_delete = (cmsUser::isAllowed($ctype['name'], 'delete', 'all') ||
                (cmsUser::isAllowed($ctype['name'], 'delete', 'own') && $album['user_id'] == $this->cms_user->id) ||
                ($photo['user_id'] == $this->cms_user->id));

        $downloads = $this->getDownloadImages($photo);
        $available_downloads = array_filter($downloads, function ($item){
            return !empty($item['link']);
        });
        $full_size_img_preset = '';
        if($available_downloads){
            $download_photo_sizes = array();
            foreach ($available_downloads as $preset => $data) {
                $download_photo_sizes[$preset] = $photo['sizes'][$preset];
            }
            $full_size_img_preset = $this->getMaxSizePresetName($download_photo_sizes);
        }

        $ordering = $this->request->get('ordering', $this->options['ordering']);
        $orderto  = $this->request->get('orderto', $this->options['orderto']);

        $photos_url_params = array();
        if($ordering != $this->options['ordering']){
            $photos_url_params['ordering'] = $ordering;
        }
        if($orderto != $this->options['orderto']){
            $photos_url_params['orderto'] = $orderto;
        }
        if($photos_url_params){
            $photos_url_params = http_build_query($photos_url_params);
        }

        if($orderto == 'asc'){

            $prev_photo = $this->model->filterEqual('album_id', $photo['album_id'])->
                    getNextPhoto($photo, $ordering, ($orderto == 'asc' ? 'desc' : 'asc'));
            $next_photo = $this->model->filterEqual('album_id', $photo['album_id'])->
                    getPrevPhoto($photo, $ordering, $orderto);

        } else {

            $next_photo = $this->model->filterEqual('album_id', $photo['album_id'])->
                    getNextPhoto($photo, $ordering, $orderto);
            $prev_photo = $this->model->filterEqual('album_id', $photo['album_id'])->
                    getPrevPhoto($photo, $ordering, ($orderto == 'asc' ? 'desc' : 'asc'));

        }

        $tpl    = 'view';
        $preset = $this->getBigPreset($photo['sizes']);
        if($this->request->isAjax()){

            $tpl = 'view_photo_container';
            if($full_size_img_preset){
                $preset = $full_size_img_preset;
            }

        }

        cmsModel::cacheResult('current_photo_item', array($album, $photo));

        return $this->cms_template->render($tpl, array(
            'photos_url_params' => $photos_url_params,
            'next_photo'    => $next_photo,
            'prev_photo'    => $prev_photo,
            'downloads'     => $downloads,
            'is_can_set_cover' => $is_can_set_cover,
            'is_can_edit'   => $is_can_edit,
            'is_can_delete' => $is_can_delete,
            'user'          => $this->cms_user,
            'preset'        => $preset,
            'preset_small'  => (!empty($this->options['preset_related']) ? $this->options['preset_related'] : $this->options['preset_small']),
            'photo'         => $photo,
            'photos'        => $this->getRelatedPhoto($photo),
            'related_title' => $this->related_title,
            'album'         => $album,
            'ctype'         => $ctype,
            'photo_details' => $this->buildPhotoDetails($photo, $album, $ctype),
            'hooks_html'    => cmsEventsManager::hookAll('photos_item_html', $photo),
            'full_size_img' => ($full_size_img_preset ? $available_downloads[$full_size_img_preset]['image'] : '')
        ));

    }

    private function getDownloadImages($photo) {

        $downloads = array();

        $presets = cmsCore::getModel('images')->getPresetsList();

        if(isset($photo['image']['original'])){
            $photo['sizes']['original'] = array(
                'width'  => $photo['width'],
                'height' => $photo['height']
            );
            $presets['original'] = LANG_PARSER_IMAGE_SIZE_ORIGINAL;
        }

        $selected = $isset = false;

        foreach ($photo['image'] as $preset => $path) {

            $show = true;

            if (!empty($this->options['download_view'][$preset]) &&
                    !$this->cms_user->isInGroups($this->options['download_view'][$preset])) {
                $show = false;
            }
            if (!empty($this->options['download_hide'][$preset]) &&
                    $this->cms_user->isInGroups($this->options['download_hide'][$preset])) {
                $show = false;
            }

            if(!$isset && $show){
                $selected = $isset = true;
            } else {
                $selected = false;
            }

            $downloads[$preset] = array(
                'link'   => ($show ? href_to('photos', 'download', array($photo['id'], $preset)).'?hash='.$this->getDownloadHash() : ''),
                'size'   => $photo['sizes'][$preset]['width'].'×'.$photo['sizes'][$preset]['height'],
                'name'   => $presets[$preset],
                'preset' => $preset,
                'image'  => ($show ? html_image_src($photo['image'], $preset, true) : ''),
                'select' => $selected
            );

        }

        return $downloads;

    }

    private function getBigPreset($sizes) {

        if(isset($sizes[$this->options['preset']])){
            return $this->options['preset'];
        }

        unset($sizes['original']);

        return $this->getMaxSizePresetName($sizes);

    }

    private function getMaxSizePresetName($sizes, $_size = 'width') {

        $__sizes = $presets = array();

        foreach ($sizes as $preset => $size) {
            $__sizes[] = $size[$_size];
            $presets[] = $preset;
        }

        return $presets[array_search(max($__sizes), $__sizes)];

    }

    private function buildPhotoDetails($photo, $album, $ctype) {

        $first_img = current($photo['image']);

        $details = array(
            array(
                'name'  => LANG_PHOTOS_FORMAT,
                'value' => strtoupper(pathinfo($first_img, PATHINFO_EXTENSION))
            ),
            array(
                'name'  => LANG_PHOTOS_SIZE,
                'value' => $photo['width'].'×'.$photo['height']
            )
        );

        if($photo['date_photo']){
            $details[] = array(
                'name'  => LANG_PHOTOS_DATE,
                'value' => html_date_time($photo['date_photo'])
            );
        }
        if(!empty($this->options['types'][$photo['type']])){
            $details[] = array(
                'name'  => LANG_PHOTOS_O_TYPE1,
                'value' => $this->options['types'][$photo['type']]
            );
        }
        $details[] = array(
            'name'  => LANG_PHOTOS_ALBUM,
            'value' => $album['title'],
            'link'  => href_to($ctype['name'], $album['slug']) . '.html'
        );
        $details[] = array(
            'name'  => LANG_HITS,
            'value' => html_spellcount($photo['hits_count'], LANG_HITS_SPELL)
        );
        $details[] = array(
            'name'  => LANG_PHOTOS_DOWNLOADS,
            'value' => $photo['downloads_count']
        );

        list($details, $photo, $album, $ctype) = cmsEventsManager::hook('build_photo_details', array($details, $photo, $album, $ctype));

        return $details;

    }

    private function getRelatedPhoto($photo) {

        if(empty($this->options['related_limit'])){ return []; }

        $this->related_title = LANG_PHOTOS_RELATED;

        $this->model->filterNotEqual('id', $photo['id']);

        $this->model->filterRelated('title', $photo['title']);

        if (cmsUser::isAllowed('albums', 'view_all')) {
            $this->model->disablePrivacyFilter();
        }

        $this->model->limit($this->options['related_limit']);

        $photos = $this->getPhotosList();
        if (!$photos) {

            $this->model->limit($this->options['related_limit']);

            $this->model->filterEqual('album_id', $photo['album_id']);

            $this->model->filterNotEqual('id', $photo['id']);

            $photos = $this->getPhotosList();

            $this->related_title = LANG_PHOTOS_OTHER_IN_ALBUM;

        }

        return $photos;

    }

}
