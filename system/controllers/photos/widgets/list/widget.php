<?php
class widgetPhotosList extends cmsWidget {

    private $photo_params = array(
        'id'      => 0,
        'user_id' => 0
    );

    public function run(){

        $user = cmsUser::getInstance();

        $photo = cmsCore::getController('photos');

        // мы в профиле?
        $current_profile = cmsModel::getCachedResult('current_profile');

        if($this->getOption('auto_user') && $current_profile){

            $this->disableCache();

            $current_profile['user_id'] = $current_profile['id'];

            $this->photo_params = $current_profile;

            $photo->model->filterEqual('user_id', $current_profile['id']);

        }

        // мы на странице записи типа контента?
        $current_ctype_item = cmsModel::getCachedResult('current_ctype_item');
        if($this->getOption('auto_ctype_item') && $current_ctype_item){

            $this->disableCache();

            $photo->model->filterRelated('title', $current_ctype_item['title']);

        }

        // мы на странице группы?
        $current_group = cmsModel::getCachedResult('current_group');
        if($this->getOption('auto_group') && $current_group){

            $this->disableCache();

            $group_albums = cmsCore::getModel('content')->limit(false)->
                    filterEqual('parent_id', $current_group['id'])->
                    filterEqual('parent_type', 'group')->
                    getContentItemsForSitemap('albums', array('id'));
            if($group_albums){
                foreach ($group_albums as $group_album) {
                    $albums_ids[] = $group_album['id'];
                }
                $photo->model->filterIn('album_id', $albums_ids);
            }

        }

        // альбом
        if($this->getOption('album_id')){

            $this->photo_params['id'] = $this->getOption('album_id');
            $this->photo_params['user_id'] = 0;

            $photo->model->filterEqual('album_id', $this->getOption('album_id'));

        }

        // типы альбомов
        switch ($this->getOption('target')) {
            case 1: // общие
                $photo->model->joinInner('con_albums', 'a', 'a.id = i.album_id');
                $photo->model->filterEqual('a.is_public', 1);
                break;
            case 2: // личные
                $photo->model->joinInner('con_albums', 'a', 'a.id = i.album_id');
                $photo->model->filterEqual('a.is_public', null);
                break;
        }

        // остальные опции
        if($this->getOption('ordering')){
            $photo->model->orderBy($this->getOption('ordering'), 'desc');
        }

        if (cmsUser::isAllowed('albums', 'view_all')) {
            $photo->model->disablePrivacyFilter();
        }

        if($this->getOption('type')){
            $photo->model->filterEqual('type', $this->getOption('type'));
        }

        if($this->getOption('orientation')){
            $photo->model->filterEqual('orientation', $this->getOption('orientation'));
        }

        if($this->getOption('width')){
            $photo->model->filterGtEqual('width', $this->getOption('width'));
        }

        if($this->getOption('height')){
            $photo->model->filterGtEqual('height', $this->getOption('height'));
        }

        $photo->model->limit($this->getOption('limit', 10));

        $photos = $photo->getPhotosList();
        if (!$photos) { return false; }

        $is_owner = cmsUser::isAllowed('albums', 'delete', 'all') ||
            (cmsUser::isAllowed('albums', 'delete', 'own') && ($user->id && $this->photo_params['user_id'] == $user->id));

        return array(
            'row_height'   => $photo->getRowHeight(),
            'user'         => $user,
            'item'         => $this->photo_params,
            'photos'       => $photos,
            'is_owner'     => $is_owner,
            'preset_small' => $photo->options['preset_small']
        );

    }

}
