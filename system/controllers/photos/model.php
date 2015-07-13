<?php
class modelPhotos extends cmsModel{

    public function addPhoto($album_id, $paths){

        $user = cmsUser::getInstance();

        $rel_paths = array();

        foreach($paths as $name=>$path){
            $rel_paths[$name] = $path;
        }

        return $this->insert('photos', array(
            'album_id' => $album_id,
            'user_id' => $user->id,
            'image' => $rel_paths
        ));

    }

    public function getPhotosCount($album_id){

        $this->filterEqual('album_id', $album_id);

        $count = $this->getCount('photos');

        $this->resetFilters();

        return $count;

    }

    public function getPhotos($album_id){

        $this->useCache("photos.{$album_id}");

        $this->select('u.nickname', 'user_nickname');
        $this->select('u.avatar', 'user_avatar');
        $this->join('{users}', 'u', 'u.id = i.user_id');

        $this->filterEqual('album_id', $album_id);

        return $this->get('photos', function($item, $model){

            $item['user'] = array(
                'id' => $item['user_id'],
                'nickname' => $item['user_nickname'],
                'avatar' => $item['user_avatar']
            );

            $item['image'] = cmsModel::yamlToArray($item['image']);

            return $item;

        });

    }

    public function getPhotosByIdsList($ids_list){

        $this->filterIn('id', $ids_list);

        return $this->get('photos', function($item, $model){
            $item['image'] = cmsModel::yamlToArray($item['image']);
            return $item;
        });

    }

    public function getOrphanPhotos(){

        $user = cmsUser::getInstance();

        $this->select('u.nickname', 'user_nickname');
        $this->select('u.avatar', 'user_avatar');
        $this->join('{users}', 'u', 'u.id = i.user_id');

        $this->filterIsNull('album_id');
        $this->filterEqual('user_id', $user->id);

        return $this->get('photos', function($item, $model){

            $item['user'] = array(
                'id' => $item['user_id'],
                'nickname' => $item['user_nickname'],
                'avatar' => $item['user_avatar']
            );

            $item['image'] = cmsModel::yamlToArray($item['image']);

            return $item;

        });

    }

    public function getPhoto($id){

        $this->select('u.nickname', 'user_nickname');
        $this->select('u.avatar', 'user_avatar');
        $this->join('{users}', 'u', 'u.id = i.user_id');

        return $this->getItemById('photos', $id, function($item, $model){

            $item['user'] = array(
                'id' => $item['user_id'],
                'nickname' => $item['user_nickname'],
                'avatar' => $item['user_avatar']
            );

            $item['image'] = cmsModel::yamlToArray($item['image']);

            return $item;

        });

    }

    public function deletePhoto($id){

        $photo = $this->getPhoto($id);

        if(is_array($photo['image'])){

            $config = cmsConfig::getInstance();

            foreach($photo['image'] as $path){
                @unlink($config->upload_path . $path);
            }

        }

        $this->filterEqual('id', $photo['album_id'])->decrement('con_albums', 'photos_count');

        cmsCache::getInstance()->clean("photos.{$photo['album_id']}");

        cmsCore::getModel('comments')->deleteComments('photos', 'photo', $id);
        cmsCore::getModel('rating')->deleteVotes('content', 'photo', $id);

        $this->delete('photos', $id);

    }

    public function deletePhotos($album_id){

        $photos = $this->getPhotos($album_id);

        if (!$photos) { return; }

        $config = cmsConfig::getInstance();
        $comments_model = cmsCore::getModel('comments');
        $rating_model = cmsCore::getModel('rating');

        foreach($photos as $photo){

            if(is_array($photo['image'])){
                foreach($photo['image'] as $path){
                    @unlink($config->upload_path . $path);
                }
            }

            $comments_model->deleteComments('photos', 'photo', $photo['id']);
            $rating_model->deleteVotes('content', $ctype_name, $id);

            $this->delete('photos', $photo['id']);

        }

        cmsCache::getInstance()->clean("photos.{$album_id}");

    }

    public function deleteUserPhotos($user_id){

        cmsCache::getInstance()->clean("photos");

        return $this->delete('photos', $user_id, 'user_id');

    }

    public function updateAlbumCoverImage($album_id, $photos){

        $ids = array_keys($photos);

        $photo = $this->getPhoto($ids[0]);

        $this->update('con_albums', $album_id, array(
            'cover_image' => $photo['image']
        ));

    }

    public function setRandomAlbumCoverImage($album_id){

        $photos = $this->getPhotos($album_id);

		if ($photos){
			$first_photo = array_shift($photos);

			$this->update('con_albums', $album_id, array(
				'cover_image' => $first_photo['image']
			));
		}

    }

    public function updateAlbumPhotosCount($album_id, $count){

        $this->
            filterEqual('id', $album_id)->
            increment('con_albums', 'photos_count', $count);

    }

    public function assignAlbumId($album_id){

        $user = cmsUser::getInstance();

        $this->filterIsNull('album_id');
        $this->filterEqual('user_id', $user->id);

        $this->updateFiltered('photos', array(
           'album_id' => $album_id
        ));

    }

    public function updatePhotoTitles($album_id, $photos){

        if (!is_array($photos)) { return false; }

        foreach($photos as $id => $title){

            $this->filterEqual('album_id', $album_id);
            $this->filterEqual('id', $id);

            if (!$title) { $title = sprintf(LANG_PHOTOS_PHOTO_UNTITLED, $id); }

            $this->updateFiltered('photos', array(
                'title' => $title
            ));

        }

        cmsCache::getInstance()->clean("photos.{$album_id}");

        return true;

    }
	
	public function renamePhoto($id, $title){
		
		if (!$title) { $title = sprintf(LANG_PHOTOS_PHOTO_UNTITLED, $id); }
		
		$photo = $this->getPhoto($id);
		
		$this->update("photos", $id, array('title' => $title));
		
		cmsCache::getInstance()->clean("photos.{$photo['album_id']}");
		
	}

    public function getAlbum($id){

        $content_model = cmsCore::getModel('content');

        $album = $content_model->getContentItem('albums', $id);

        if (!$album) { return false; }

        $album['ctype'] = $content_model->getContentTypeByName('albums');

        return $album;

    }

//============================================================================//
//============================================================================//

    public function getRatingTarget($subject, $id){

        $item = $this->getPhoto($id);

        return $item;

    }


    public function updateRating($subject, $id, $rating){

        $this->update('photos', $id, array('rating' => $rating));

    }

    public function getUpdatedRating($subject, $id, $vote){

        $item = $this->getPhoto($id);

        $rating = (int)$item['rating'] + $vote['score'];

        $this->update('photos', $id, array('rating' => $rating));

        $this->filterEqual('id', $item['album_id'])->increment('con_albums', 'rating', $vote['score']);

        return $rating;

    }

//============================================================================//
//============================================================================//

    public function updateCommentsCount($subject, $id, $comments_count){

        $this->update('photos', $id, array('comments' => $comments_count));

        return true;

    }

    public function getTargetItemInfo($ctype_name, $id){

        $item = $this->getPhoto($id);

        if (!$item){ return false; }

        return array(
            'url' => href_to_rel('photos', 'view', $id),
            'title' => $item['title']
        );

    }

}
