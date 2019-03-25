<?php

class modelWall extends cmsModel {

    public function getEntriesCount(){

        $this->useCache('wall.count');

        return $this->getCount('wall_entries');

    }

    public function getEntries($user){

        $this->useCache('wall.entries');

        $this->select('COUNT(e.id)', 'replies_count')->
                joinLeft('wall_entries', 'e', 'e.parent_id = i.id')->
                joinUser()->
                groupBy('i.id');

        $this->joinSessionsOnline();

        return $this->get('wall_entries', function($item, $model) use($user) {

            $item['user'] = array(
                'id'        => $item['user_id'],
                'nickname'  => $item['user_nickname'],
                'is_online' => $item['is_online'],
                'avatar'    => $item['user_avatar']
            );

            $item['is_new'] = (strtotime($item['date_pub']) > strtotime($user->date_log));

            return $item;

        });

    }

    public function getReplies($parent_id){

        $this->useCache('wall.entries');

        $this->joinUser()->joinSessionsOnline()->
                filterEqual('parent_id', $parent_id)->
                orderBy('date_pub', 'asc');

        return $this->get('wall_entries', function($item, $model){

            $item['user'] = array(
                'id'        => $item['user_id'],
                'nickname'  => $item['user_nickname'],
                'is_online' => $item['is_online'],
                'avatar'    => $item['user_avatar']
            );

            $item['replies_count'] = 0;

            return $item;

        });

    }

    public function getEntry($id){

        $entry = $this->
                    joinUser()->joinSessionsOnline()->
                    getItemById('wall_entries', $id, function($item, $model){

                        $item['user'] = array(
                            'id'        => $item['user_id'],
                            'nickname'  => $item['user_nickname'],
                            'is_online' => $item['is_online'],
                            'avatar'    => $item['user_avatar']
                        );

                        return $item;

                    });

        return $entry;

    }

    public function getEntryPageNumber($id, $target, $perpage){

        $this->selectOnly('id')->limit(false)->
                filterEqual('profile_id', $target['profile_id'])->
                filterEqual('parent_id', 0)->
                filterEqual('profile_type', $target['profile_type']);

        $entries = $this->get('wall_entries');

        $index = 0;

        if($entries){
            foreach ($entries as $e){
                $index++;
                if ($e['id'] == $id){ break; }
            }
        }

        if (!$index) { return 1; }

        return ceil($index / $perpage);

    }

//============================================================================//
//============================================================================//

    public function addEntry($entry){

        // для записей-ответов ставим дату у родителя
        if(!empty($entry['parent_id'])){
            $this->update('wall_entries', $entry['parent_id'], array(
                'date_last_reply' => null
            ));
        }

        $entry['date_last_reply'] = null;

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
            'date_last_modified' => null,
            'content'            => $content,
            'content_html'       => $content_html
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

}
