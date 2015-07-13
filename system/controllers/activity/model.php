<?php
class modelActivity extends cmsModel{

    public function addType($type){

        return $this->insert('activity_types', $type);

    }

    public function updateType($id, $type){

        return $this->update('activity_types', $id, $type);

    }

    public function getType($controller, $name){

        return $this->
                    filterEqual('controller', $controller)->
                    filterEqual('name', $name)->
                    getItem('activity_types');

    }

    public function getTypes(){

        return $this->orderBy('title')->get('activity_types');

    }

    public function enableTypes($types_ids){

        $this->
            filterGt('id', 0)->
            updateFiltered('activity_types', array('is_enabled'=>0));

        $this->
            filterIn('id', $types_ids)->
            updateFiltered('activity_types', array('is_enabled'=>1));

    }

    public function deleteType($controller, $name){

        return $this->
                    filterEqual('controller', $controller)->
                    filterEqual('name', $name)->
                    deleteFiltered('activity_types');

    }

//============================================================================//
//============================================================================//

    public function addEntry($entry){

        cmsCache::getInstance()->clean("activity.entries");

        return $this->insert('activity', $entry);

    }

    public function updateEntry($type_id, $subject_id, $entry){

        cmsCache::getInstance()->clean("activity.entries");

        return $this->
                    filterEqual('type_id', $type_id)->
                    filterEqual('subject_id', $subject_id)->
                    updateFiltered('activity', $entry);

    }

    public function deleteEntry($type_id, $subject_id){

        cmsCache::getInstance()->clean("activity.entries");

        return $this->
                    filterEqual('type_id', $type_id)->
                    filterEqual('subject_id', $subject_id)->
                    deleteFiltered('activity');

    }

    public function deleteEntryById($entry_id){

        cmsCache::getInstance()->clean("activity.entries");

        return $this->delete('activity', $entry_id);

    }

    public function deleteEntries($type_id){

        cmsCache::getInstance()->clean("activity.entries");

        return $this->delete('activity', $type_id, 'type_id');

    }

    public function deleteUserEntries($user_id){

        cmsCache::getInstance()->clean("activity.entries");

        return $this->delete('activity', $user_id, 'user_id');

    }

//============================================================================//
//============================================================================//

    public function getEntriesCount(){

        return $this->getCount('activity');

    }

    public function getEntries(){

        $this->select('u.nickname', 'user_nickname');
        $this->select('u.avatar', 'user_avatar');
        $this->join('{users}', 'u', 'u.id = i.user_id');

        $this->select('t.description', 'description');
        $this->join('activity_types', 't', 't.id = i.type_id');

        if (!$this->order_by){
            $this->orderBy('date_pub', 'desc');
        }

        $this->useCache("activity.entries");

        return $this->get('activity', function($item, $model){

            $item['user'] = array(
                'id' => $item['user_id'],
                'nickname' => $item['user_nickname'],
                'avatar' => $item['user_avatar']
            );

            if (!empty($item['subject_url'])){
                $max_title_len = 50;
                $item['subject_title'] = mb_strlen($item['subject_title'])>$max_title_len ? mb_substr($item['subject_title'], 0, $max_title_len).'...' : $item['subject_title'];
                $link = '<a href="'.$item['subject_url'].'">'. $item['subject_title'].'</a>';
            } else {
                $link = $item['subject_title'];
            }

            $item['images'] = cmsModel::yamlToArray($item['images']);
			
			if ($item['images']){
				
				$config = cmsConfig::getInstance();
				$images_exist = array();
				
				foreach($item['images'] as $idx=>$image){
					if (mb_substr($image['src'], 0, 7)!='http://') {
						if (!file_exists($config->upload_path . '/' . $image['src'])){
							continue;
						}						
						$image['src'] = $config->upload_host . '/' . $image['src'];
					}
					$images_exist[] = $image;
				}
				
				$item['images'] = $images_exist;
				
			}

            $item['description'] = sprintf($item['description'], $link);

            $item['date_diff'] = string_date_age_max($item['date_pub'], true);

            return $item;

        });

    }

}
