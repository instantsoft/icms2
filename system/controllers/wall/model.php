<?php

class modelWall extends cmsModel {

    public function getEntriesCount() {

        $this->useCache('wall.count');

        return $this->getCount('wall_entries');
    }

    public function getEntries( cmsUser $user, $actions = false) {

        $this->useCache('wall.entries');

        $this->select('COUNT(e.id)', 'replies_count')->
                joinLeft('wall_entries', 'e', 'e.parent_id = i.id')->
                joinUser()->
                groupBy('i.id');

        $this->joinSessionsOnline();

        return $this->get('wall_entries', function ($item, $model) use ($user, $actions) {

            if (empty($item['replies_count'])){ $item['replies_count'] = 0; }

            $item['user'] = [
                'id'        => $item['user_id'],
                'slug'      => $item['user_slug'],
                'nickname'  => $item['user_nickname'],
                'is_online' => $item['is_online'],
                'avatar'    => $item['user_avatar']
            ];

            $item['is_new'] = (strtotime($item['date_pub']) > strtotime($user->date_log));

            $item['actions'] = [];

            if (is_array($actions)) {
                foreach ($actions as $key => $action) {

                    if (isset($action['handler'])) {
                        $is_active = $action['handler']($item);
                    } else {
                        $is_active = true;
                    }

                    if (!$is_active) { continue; }

                    if (empty($action['href'])) { continue; }

                    if (isset($action['handler_class'])) {
                        $action['class'] = $action['handler_class']($item);
                    }

                    foreach ($item as $cell_id => $cell_value) {

                        if (is_array($cell_value) || is_object($cell_value)) {
                            continue;
                        }
                        if (!$cell_value) { $cell_value = ''; }

                        foreach (['href', 'title', 'hint', 'onclick'] as $replaceable_key) {
                            if(isset($action[$replaceable_key])){
                                $action[$replaceable_key] = str_replace('{' . $cell_id . '}', $cell_value, $action[$replaceable_key]);
                            }
                        }
                    }
                    $item['actions'][$key] = $action;
                }
            }

            return $item;
        });
    }

    public function getEntry($id) {

        return $this->joinUser()->joinSessionsOnline()->
                getItemById('wall_entries', $id, function ($item, $model) {

            $item['user'] = [
                'id'        => $item['user_id'],
                'slug'      => $item['user_slug'],
                'nickname'  => $item['user_nickname'],
                'is_online' => $item['is_online'],
                'avatar'    => $item['user_avatar']
            ];

            return $item;
        });
    }

    public function getEntryPageNumber($id, $target, $perpage) {

        $this->selectOnly('id')->limit(false)->
                filterEqual('profile_id', $target['profile_id'])->
                filterEqual('parent_id', 0)->
                filterEqual('profile_type', $target['profile_type']);

        $entries = $this->get('wall_entries');

        $index = 0;

        if ($entries) {
            foreach ($entries as $e) {
                $index++;
                if ($e['id'] == $id) {
                    break;
                }
            }
        }

        if (!$index) {
            return 1;
        }

        return ceil($index / $perpage);
    }

    public function addEntry($entry) {

        // для записей-ответов ставим дату у родителя
        if (!empty($entry['parent_id'])) {
            $this->update('wall_entries', $entry['parent_id'], [
                'date_last_reply' => null
            ]);
        }

        $entry['date_last_reply'] = null;

        $id = $this->insert('wall_entries', $entry);

        cmsCache::getInstance()->clean('wall.entries');
        cmsCache::getInstance()->clean('wall.count');

        return $id;
    }

    public function updateEntryStatusId($id, $status_id) {

        $result = $this->update('wall_entries', $id, [
            'status_id' => $status_id
        ]);

        cmsCache::getInstance()->clean('wall.entries');
        cmsCache::getInstance()->clean('wall.count');

        return $result;
    }

    public function updateEntryContent($id, $content, $content_html) {

        $result = $this->update('wall_entries', $id, [
            'date_last_modified' => null,
            'content'            => $content,
            'content_html'       => $content_html
        ]);

        cmsCache::getInstance()->clean('wall.entries');
        cmsCache::getInstance()->clean('wall.count');

        return $result;
    }

    public function deleteEntry($id) {

        $this->delete('wall_entries', $id);

        $this->filterEqual('parent_id', $id)->deleteFiltered('wall_entries');

        cmsCache::getInstance()->clean('wall.entries');
        cmsCache::getInstance()->clean('wall.count');

        return true;
    }

    public function deleteUserEntries($user_id) {

        $this->delete('wall_entries', $user_id, 'user_id');
        $this->delete('wall_entries', $user_id, 'profile_id');

        cmsCache::getInstance()->clean('wall.entries');
        cmsCache::getInstance()->clean('wall.count');
    }

}
