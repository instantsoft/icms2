<?php

class modelWall extends cmsModel{

//============================================================================//
//============================================================================//

    public function getEntriesCount($profile_type, $profile_id, $only_root=true){

        $this->useCache('wall.count');

        $this->
            filterEqual('profile_id', $profile_id)->
            filterEqual('profile_type', $profile_type);

        if ($only_root){
            $this->filterEqual('parent_id', 0);
        }

        $count = $this->getCount('wall_entries');

        $this->resetFilters();

        return $count;

    }

    public function getEntries($profile_type, $profile_id, $page=false, $parent_id=0){

        $this->useCache('wall.entries');

        $entries = $this->
                        select('COUNT(e.id)', 'replies_count')->
                        joinLeft('wall_entries', 'e', 'e.parent_id = i.id')->
                        joinUser()->
                        filterEqual('profile_id', $profile_id)->
                        filterEqual('profile_type', $profile_type)->
                        filterEqual('parent_id', $parent_id)->
                        groupBy('i.id')->
                        orderBy('date_pub', 'desc');

        if ($page){
            $this->limitPage($page, wall::$perpage);
        }

        $entries = $this->get('wall_entries', function($item, $model){

            $item['user'] = array(
                'id' => $item['user_id'],
                'nickname' => $item['user_nickname'],
                'avatar' => $item['user_avatar']
            );

            return $item;

        });

        return $entries;

    }

    public function getReplies($parent_id){

        $this->useCache('wall.entries');

        $entries = $this->
                        joinUser()->
                        filterEqual('parent_id', $parent_id)->
                        orderBy('date_pub', 'asc');

        $entries = $this->get('wall_entries', function($item, $model){

            $item['user'] = array(
                'id' => $item['user_id'],
                'nickname' => $item['user_nickname'],
                'avatar' => $item['user_avatar']
            );

            $item['replies_count'] = 0;

            return $item;

        });

        return $entries;

    }

    public function getEntry($id){

        $entry = $this->
                        joinUser()->
                        getItemById('wall_entries', $id, function($item, $model){

                            $item['user'] = array(
                                'id' => $item['user_id'],
                                'nickname' => $item['user_nickname'],
                                'avatar' => $item['user_avatar']
                            );

                            return $item;

                        });

        return $entry;

    }

    public function getEntryPageNumber($id, $target, $perpage){

        $entries = $this->getEntries($target['profile_type'], $target['profile_id']);

        $index = 0;

        foreach ($entries as $e){
            $index++;
            if ($e['id'] == $id){ break; }
        }

        if (!$index) { return 1; }

        return ceil($index / $perpage);

    }

//============================================================================//
//============================================================================//

    public function addEntry($entry){

        $id = $this->insert('wall_entries', $entry);

        cmsCache::getInstance()->clean('wall.entries');
        cmsCache::getInstance()->clean('wall.count');

        return $id;

    }

    public function updateEntryStatusId($id, $status_id){

        $result = $this->update('wall_entries', $id, array(
            'status_id'=>$status_id,
        ));

        cmsCache::getInstance()->clean('wall.entries');
        cmsCache::getInstance()->clean('wall.count');

        return $result;

    }

    public function updateEntryContent($id, $content, $content_html){

        $result = $this->update('wall_entries', $id, array(
            'content'=>$content,
            'content_html'=>$content_html
        ));

        cmsCache::getInstance()->clean('wall.entries');
        cmsCache::getInstance()->clean('wall.count');

        return $result;

    }

    public function deleteEntry($id){

        $this->delete('wall_entries', $id);

        $this->filterEqual('parent_id', $id)->deleteFiltered('wall_entries');

        cmsCache::getInstance()->clean('wall.entries');
        cmsCache::getInstance()->clean('wall.count');

        return true;

    }

    public function deleteUserEntries($user_id){

        $this->delete('wall_entries', $user_id, 'user_id');
        $this->delete('wall_entries', $user_id, 'profile_id');

        cmsCache::getInstance()->clean('wall.entries');
        cmsCache::getInstance()->clean('wall.count');

    }

//============================================================================//
//============================================================================//

}
