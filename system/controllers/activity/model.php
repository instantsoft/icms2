<?php

class modelActivity extends cmsModel {

    private $is_joined_user = false;

    public function addType($type) {
        return $this->insert('activity_types', $type);
    }

    public function updateType($id, $type) {
        return $this->update('activity_types', $id, $type);
    }

    public function getType($controller, $name) {
        return $this->filterEqual('controller', $controller)->
            filterEqual('name', $name)->
            getItem('activity_types');
    }

    public function getTypes() {
        return $this->orderBy('title')->get('activity_types');
    }

    public function enableTypes($types_ids) {

        $this->filterGt('id', 0)->
            updateFiltered('activity_types', ['is_enabled' => 0]);

        $this->filterIn('id', $types_ids)->
            updateFiltered('activity_types', ['is_enabled' => 1]);

    }

    public function deleteType($controller, $name) {
        return $this->filterEqual('controller', $controller)->
            filterEqual('name', $name)->
            deleteFiltered('activity_types');
    }

//============================================================================//
//============================================================================//

    public function addEntry($entry) {

        cmsCache::getInstance()->clean('activity.entries');

        return $this->insert('activity', $entry);
    }

    public function updateEntry($type_id, $subject_id, $entry) {

        cmsCache::getInstance()->clean('activity.entries');

        return $this->filterEqual('type_id', $type_id)->
            filterEqual('subject_id', $subject_id)->
            updateFiltered('activity', $entry);
    }

    public function deleteEntry($type_id, $subject_id) {

        cmsCache::getInstance()->clean('activity.entries');

        if (is_array($subject_id)) {
            $this->filterIn('subject_id', $subject_id);
        } else {
            $this->filterEqual('subject_id', $subject_id);
        }

        return $this->filterEqual('type_id', $type_id)->deleteFiltered('activity');
    }

    public function deleteEntryById($entry_id) {

        cmsCache::getInstance()->clean('activity.entries');

        return $this->delete('activity', $entry_id);
    }

    public function deleteEntries($type_id) {

        cmsCache::getInstance()->clean('activity.entries');

        return $this->delete('activity', $type_id, 'type_id');
    }

    public function deleteUserEntries($user_id) {

        cmsCache::getInstance()->clean('activity.entries');

        return $this->delete('activity', $user_id, 'user_id');
    }

//============================================================================//
//============================================================================//

    private function joinUserNotDeleted() {

        if ($this->is_joined_user) {
            return $this;
        }

        $this->select('u.nickname', 'user_nickname');
        $this->select('u.avatar', 'user_avatar');
        $this->select('u.slug', 'user_slug');
        $this->joinLeft('{users}', 'u', 'u.id = i.user_id AND u.is_deleted IS NULL');

        $this->is_joined_user = true;

        return $this;
    }

    public function getEntriesCount() {

        if (!$this->hidden_parents_filter_disabled) {
            $this->filterHiddenParents();
        }

        $this->joinUserNotDeleted();

        $this->useCache('activity.entries');

        return $this->getCount('activity');
    }

    public function getEntries() {

        $this->joinUserNotDeleted();

        $this->selectTranslatedField('t.description', 'activity_types');
        $this->joinLeft('activity_types', 't', 't.id = i.type_id');

        $this->joinSessionsOnline();

        if (!$this->order_by) {
            $this->orderBy('date_pub', 'desc');
        }

        if (!$this->hidden_parents_filter_disabled) {
            $this->filterHiddenParents();
        }

        $this->useCache('activity.entries');

        $config = cmsConfig::getInstance();
        $user   = cmsUser::getInstance();

        return $this->get('activity', function ($item, $model) use ($config, $user) {

            $item['user'] = [
                'id'        => $item['user_id'],
                'slug'      => $item['user_slug'],
                'nickname'  => $item['user_nickname'],
                'is_online' => $item['is_online'],
                'avatar'    => $item['user_avatar']
            ];

            if (!empty($item['subject_url'])) {

                $item['subject_url'] = rel_to_href($item['subject_url']);
                $max_title_len = 50;
                $item['subject_title'] = mb_strlen($item['subject_title']) > $max_title_len ? mb_substr($item['subject_title'], 0, $max_title_len) . '...' : $item['subject_title'];

                $link = '<a href="' . $item['subject_url'] . '">' . $item['subject_title'] . '</a>';
            } else {
                $link = $item['subject_title'];
            }

            if (!empty($item['reply_url'])) {
                $item['reply_url'] = rel_to_href($item['reply_url']);
            }

            $item['images'] = cmsModel::yamlToArray($item['images']);

            if ($item['images']) {

                $images_exist = [];

                foreach ($item['images'] as $key => $image) {
                    if (strpos($image['src'], 'http') !== 0) {
                        if (!file_exists($config->upload_path . $image['src'])) {
                            continue;
                        }
                        $image['src'] = $config->upload_host . '/' . $image['src'];
                    }
                    $image['url']   = rel_to_href($image['url']);
                    $images_exist[] = $image;
                }

                if (!$images_exist) {
                    $model->deleteEntryById($item['id']);
                }

                $item['images'] = $images_exist;
            }

            if($item['description']){
                $item['description'] = sprintf($item['description'], $link);
            }

            $item['date_diff'] = string_date_age_max($item['date_pub'], true);

            $item['is_new'] = (strtotime($item['date_pub']) > strtotime($user->date_log));

            return $item;
        });
    }

}
