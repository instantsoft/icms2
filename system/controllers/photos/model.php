<?php
class modelPhotos extends cmsModel{

	public $config = array();

    public function __construct() {

        parent::__construct();

        $this->config = cmsController::loadOptions('photos');

        if(!empty($this->config['types'])){
            $this->config['types'] = string_explode_list($this->config['types']);
        }

	}

    public static function getOrderList(){
        return array(
            'date_pub'   => LANG_PHOTOS_DATE_PUB,
            'date_photo' => LANG_PHOTOS_DATE_PHOTO,
            'rating'     => LANG_PHOTOS_RATING,
            'hits_count' => LANG_PHOTOS_HITS_COUNT,
            'comments'   => LANG_PHOTOS_COMMENTS
        );
    }

    public static function getOrientationList(){
        return array(
            ''          => LANG_PHOTOS_ALL_ORIENT,
            'square'    => LANG_PHOTOS_SQUARE,
            'landscape' => LANG_PHOTOS_LANDSCAPE,
            'portrait'  => LANG_PHOTOS_PORTRAIT
        );
    }

    public function filterApprovedOnly(){

        if ($this->approved_filtered) { return $this; }

        // Этот фильтр может применяться при подсчете числа записей
        // и при выборке самих записей
        // используем флаг чтобы фильтр не применился дважды
        $this->approved_filtered = true;

        $this->join('con_albums', 'al', 'al.id = i.album_id');

        return $this->filterEqual('al.is_approved', 1);

    }

    public function getPhotosCount($album_id){

        if (!$this->privacy_filter_disabled) { $this->filterPrivacy(); }
        if (!$this->approved_filter_disabled) { $this->filterApprovedOnly(); }

        $this->filterEqual('album_id', $album_id);

        $count = $this->getCount('photos');

        $this->resetFilters();

        return $count;

    }

    public function getPhotos($id = 0, $filter_field = 'album_id', $only_fields = false, $item_callback = false){

        if(!$only_fields){

            if($filter_field == 'album_id'){
                $this->useCache("photos.{$id}");
            }

            $this->joinUser();

            if (!$this->privacy_filter_disabled) { $this->filterPrivacy(); }
            if (!$this->approved_filter_disabled) { $this->filterApprovedOnly(); }

        } else {

            $this->select = array();

            foreach ($only_fields as $field) {
                $this->select($field);
            }

        }

        if($filter_field){
            $this->filterEqual($filter_field, $id);
        }

        return $this->get('photos', function($item, $model) use ($item_callback){

            if(isset($item['user_nickname'])){
                $item['user'] = array(
                    'slug'     => $item['user_slug'],
                    'id'       => $item['user_id'],
                    'nickname' => $item['user_nickname'],
                    'avatar'   => $item['user_avatar']
                );
            }

            if(isset($item['image'])){
                $item['image'] = cmsModel::yamlToArray($item['image']);
            }

            if(isset($item['sizes'])){
                $item['sizes'] = cmsModel::yamlToArray($item['sizes']);
            }

            if(!empty($item['type']) && !empty($model->config['types'])){
                 $item['type'] = $model->config['types'][$item['type']];
            }

            if(is_callable($item_callback)){
                $item = call_user_func_array($item_callback, array($item, $model));
                if ($item === false){ return false; }
            }

            return $item;

        }, false);

    }

    public function getUserPhotos($user_id, $only_fields = false, $item_callback = false) {
        return $this->getPhotos($user_id, 'user_id', $only_fields, $item_callback);
    }

    public function getOrphanPhotos($user_id){
        $this->disablePrivacyFilter();
        $this->disableApprovedFilter();
        return $this->filterIsNull('slug')->getUserPhotos($user_id, false, function($item, $model){
            $item['is_private'] = 0;
            return $item;
        });
    }

    public function getPhotosByIdsList($ids_list){

        $this->filterIn('id', $ids_list);

        return $this->get('photos', function($item, $model){

            $item['image'] = cmsModel::yamlToArray($item['image']);
            $item['sizes'] = cmsModel::yamlToArray($item['sizes']);

            return $item;

        });

    }

    public function getPhoto($id){

        if(is_numeric($id)){
            $this->filterEqual('id', $id);
        } else {
            $this->filterEqual('slug', $id);
        }

        $this->joinUser()->joinSessionsOnline();

        return $this->getItem('photos', function($item, $model){

            $item['user'] = array(
                'id'       => $item['user_id'],
                'slug'     => $item['user_slug'],
                'nickname' => $item['user_nickname'],
                'is_online' => $item['is_online'],
                'avatar'   => $item['user_avatar']
            );

            $item['image'] = cmsModel::yamlToArray($item['image']);
            $item['sizes'] = cmsModel::yamlToArray($item['sizes']);
            $item['exif']  = cmsModel::yamlToArray($item['exif']);

            return $item;

        });

    }

    public function getPrevPhoto($item, $order_field, $orderto) {

        $this->filterStart()->
            filterGt($order_field, $item[$order_field])->
            filterOr()->
            filterStart()->
                filterEqual($order_field, $item[$order_field])->
                filterAnd()->
                filterGt('id', $item['id'])->
            filterEnd()->
        filterEnd();

        $this->orderByList(array(
            array(
                'by' => $order_field,
                'to' => $orderto
            ),
            array(
                'by' => 'id',
                'to' => $orderto
            )
        ));

        return $this->getItem('photos', function($item, $model){
            $item['image'] = cmsModel::yamlToArray($item['image']);
            $item['sizes'] = cmsModel::yamlToArray($item['sizes']);
            return $item;
        });

    }

    public function getNextPhoto($item, $order_field, $orderto) {

        $this->filterStart()->
            filterLt($order_field, $item[$order_field])->
            filterOr()->
            filterStart()->
                filterEqual($order_field, $item[$order_field])->
                filterAnd()->
                filterLt('id', $item['id'])->
            filterEnd()->
        filterEnd();

        $this->orderByList(array(
            array(
                'by' => $order_field,
                'to' => $orderto
            ),
            array(
                'by' => 'id',
                'to' => $orderto
            )
        ));

        return $this->getItem('photos', function($item, $model){
            $item['image'] = cmsModel::yamlToArray($item['image']);
            $item['sizes'] = cmsModel::yamlToArray($item['sizes']);
            return $item;
        });

    }

	public function incrementCounter($id, $field = 'hits_count'){

        if(cmsUser::hasCookie($field.$id)){
            return false;
        }

		$this->filterEqual('id', $id)->increment('photos', $field);

        cmsUser::setCookie($field.$id, 1, 2592000);

        return true;

	}

    /**************************************************************************/

    public function deletePhoto($id_or_photo){

        $photo = is_array($id_or_photo) ? $id_or_photo : $this->getPhoto($id_or_photo);
        if (!$photo) { return false; }

        return $this->deletePhotosByPhotoList(array($photo), $photo['album_id']);

    }

    public function deletePhotosByPhotoList($photos, $album_id = false){

        $config = cmsConfig::getInstance();
        $comments_model = cmsCore::getModel('comments');
        $rating_model   = cmsCore::getModel('rating');

        foreach($photos as $photo){

            if(is_array($photo['image'])){
                foreach($photo['image'] as $path){
                    @unlink($config->upload_path . $path);
                }
            }

            $comments_model->deleteComments('photos', 'photo', $photo['id']);
            $rating_model->deleteVotes('photos', 'photo', $photo['id']);

            $this->delete('photos', $photo['id']);

        }

        if($album_id){

            $this->setRandomAlbumCoverImage($album_id);

            $this->updateAlbumPhotosCount($album_id);

            cmsCache::getInstance()->clean("photos.{$album_id}");

        } else {
            cmsCache::getInstance()->clean('photos');
        }

        return true;

    }

    public function deletePhotos($album_id){

        $photos = $this->getPhotos($album_id, 'album_id', array('image', 'id'));
        if (!$photos) { return false; }

        return $this->deletePhotosByPhotoList($photos, $album_id);

    }

    public function deleteUserPhotos($user_id){

        $photos = $this->getUserPhotos($user_id, array('image', 'id'));
        if (!$photos) { return false; }

        return $this->deletePhotosByPhotoList($photos);

    }

    /**************************************************************************/

    public function updateAlbumCoverImage($album_id, $photo_ids){

        $photo = $this->getPhoto($photo_ids[0]);

        $this->update('con_albums', $album_id, array(
            'cover_image' => $photo['image']
        ));

        cmsCache::getInstance()->clean('content.list.albums');
        cmsCache::getInstance()->clean('content.item.albums');

        return true;

    }

    public function setRandomAlbumCoverImage($album_id){

        $this->limit(50);
        $this->orderBy('date_pub', 'desc');

        $photos = $this->getPhotos($album_id, 'album_id', array('image'));

		if ($photos){
            shuffle($photos);
			$first_photo = array_shift($photos);
		} else {
            $first_photo = array('image' => null);
        }

        $this->update('con_albums', $album_id, array(
            'cover_image' => $first_photo['image']
        ));

        cmsCache::getInstance()->clean('content.list.albums');
        cmsCache::getInstance()->clean('content.item.albums');

        return true;

    }

    public function updateAlbumPhotosCount($album_id){

        if($album_id){
            $this->db->query("UPDATE {#}con_albums SET photos_count=(SELECT COUNT(id) FROM {#}photos WHERE album_id = '{$album_id}') WHERE id='{$album_id}' LIMIT 1");
            cmsCache::getInstance()->clean('content.list.albums');
            cmsCache::getInstance()->clean('content.item.albums');
        }

        return $this;

    }

    public function addPhoto($data){

        $id = $this->insert('photos', $data);

        cmsCache::getInstance()->clean("photos.{$data['album_id']}");

        return $id;

    }

    public function getPhotoSlug($item, $check_slug = true){

        $url_pattern = empty($this->config['url_pattern']) ? '{id}-{title}' : $this->config['url_pattern'];

        $slug_len = 100; $matches = array(); $pattern = trim($url_pattern, '/');

        preg_match_all('/{([a-z0-9\_]+)}/i', $pattern, $matches);

        if (!$matches) { return lang_slug($item['id']); }

        list($tags, $names) = $matches;

        foreach($names as $idx => $field_name){

            if (!empty($item[$field_name])){

                $value = str_replace('/', '', $item[$field_name]);

                if ($field_name == 'type' && !empty($this->config['types'])){
                    $value = $this->config['types'][$value];
                    $value = trim($value, '/');
                }

                $pattern = str_replace($tags[$idx], $value, $pattern);

            }

        }

        $slug = lang_slug($pattern);

        $slug = mb_substr($slug, 0, $slug_len);

        if(!$check_slug){
            return $slug;
        }

        if($this->filterNotEqual('id', $item['id'])->
                filterEqual('slug', $slug)->
                getFieldFiltered('photos', 'id')){

            $id_len = strlen((string)$item['id'])+1;

            $slug = mb_substr($slug, 0, ($slug_len - $id_len));
            $slug .= '-'.$item['id'];

        }

        return $slug;

    }

    public function updatePhotoList($photo_list, $generate_slug = false, $is_assign = false){

        if (!$photo_list || !is_array($photo_list)) { return false; }

        $ids = array_keys($photo_list);

        $photos = $this->getPhotosByIdsList($ids);

        $return = array();

        foreach($photo_list as $photo_id => $photo){

            $_photo = array_merge($photos[$photo_id], $photo);

            if($generate_slug){

                $photo['slug'] = $_photo['slug'] = $this->getPhotoSlug($_photo);

                if(!$_photo['is_private']){
                    $return[$photo_id] = $_photo;
                }

            }

            $this->filterEqual('id', $photo_id);

            $this->updateFiltered('photos', $photo);

        }

        cmsCache::getInstance()->clean("photos.{$_photo['album_id']}");

        if($is_assign){
            $this->updateAlbumCoverImage($_photo['album_id'], $ids);
            $this->updateAlbumPhotosCount($_photo['album_id']);
        }

        return $return;

    }

    public function assignPhotoList($photo_list){

        return $this->updatePhotoList($photo_list, true, true);

    }

    public function getAlbum($id){

        $content_model = cmsCore::getModel('content');

        $album = $content_model->getContentItem('albums', $id);
        if (!$album) { return false; }

        $album['ctype'] = $content_model->getContentTypeByName('albums');
        $album['ctype_name'] = $album['ctype']['name'];

        return $album;

    }

//============================================================================//
//============================================================================//

    public function getRatingTarget($subject, $id){

        $item = $this->getPhoto($id);

        if($item){
            $item['page_url'] = href_to('photos', $item['slug'].'.html');
        }

        return $item;

    }


    public function updateRating($subject, $id, $rating){

        $item = $this->getPhoto($id);
        if (!$item){ return false; }

        $this->update('photos', $item['id'], array('rating' => $rating));

        cmsCache::getInstance()->clean("photos.{$item['album_id']}");

        return true;

    }

    public function getUpdatedRating($subject, $id, $vote){

        $item = $this->getPhoto($id);

        $rating = (int)$item['rating'] + $vote['score'];

        $this->update('photos', $id, array('rating' => $rating));

        $this->filterEqual('id', $item['album_id'])->increment('con_albums', 'rating', $vote['score']);

        cmsCache::getInstance()->clean('content.list.albums');
        cmsCache::getInstance()->clean('content.item.albums');

        return $rating;

    }

//============================================================================//
//============================================================================//

    public function updateCommentsCount($subject, $id, $comments_count){

        $item = $this->getPhoto($id);
        if (!$item){ return false; }

        $this->update('photos', $item['id'], array('comments' => $comments_count));

        cmsCache::getInstance()->clean("photos.{$item['album_id']}");

        return true;

    }

    public function getTargetItemInfo($ctype_name, $id){

        $item = $this->getPhoto($id);
        if (!$item){ return false; }

        return array(
            'url'   => href_to_rel('photos', $item['slug'].'.html'),
            'title' => $item['title']
        );

    }

}
