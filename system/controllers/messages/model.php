<?php

class modelMessages extends cmsModel {

    public function getDefaultNoticeOptions(){

        return array(
            'is_closeable' => true
        );

    }

//============================================================================//
//============================================================================//

    public function addContact($user_id, $contact_id){

        return $this->insert('{users}_contacts', array(
            'user_id'    => $user_id,
            'contact_id' => $contact_id
        ));

    }

    public function updateContactsDateLastMsg($user_id, $contact_id, $is_both=true){

        $this->filterEqual('user_id', $user_id);
        $this->filterEqual('contact_id', $contact_id);
        $this->updateFiltered('{users}_contacts', array('date_last_msg'=>null));

        if ($is_both){
            $this->filterEqual('user_id', $contact_id);
            $this->filterEqual('contact_id', $user_id);
            $this->updateFiltered('{users}_contacts', array('date_last_msg'=>null));
        }

    }

//============================================================================//
//============================================================================//

    public function getContacts($user_id){

        $this->select('u.id', 'id');
        $this->select('u.nickname', 'nickname');
        $this->select('u.avatar', 'avatar');
        $this->select('u.is_admin', 'is_admin');
        $this->select('u.date_log', 'date_log');
        $this->select('IFNULL(COUNT(m.id), 0)', 'new_messages');

        $this->join('{users}', 'u', 'u.id = i.contact_id');
        $this->joinSessionsOnline();
        $this->joinLeft('{users}_messages', 'm', 'm.from_id = i.contact_id AND m.to_id = i.user_id AND m.is_new = 1 AND m.is_deleted IS NULL');

        $this->filterEqual('user_id', $user_id);

        $this->groupBy('i.contact_id');

        $this->orderBy('date_last_msg', 'desc');

        return $this->get('{users}_contacts');

    }

    public function getContactsCount($user_id){

        $this->filterEqual('user_id', $user_id);

        return $this->getCount('{users}_contacts');

    }

    public function getContact($user_id, $contact_id){

        $this->select('u.id', 'id');
        $this->select('u.nickname', 'nickname');
        $this->select('u.date_log', 'date_log');
        $this->select('u.avatar', 'avatar');
        $this->select('u.is_admin', 'is_admin');
        $this->select('u.privacy_options', 'privacy_options');
        $this->select('COUNT(g.user_id)', 'is_ignored');
        $this->join('{users}', 'u', 'u.id = i.contact_id');
        $this->joinSessionsOnline();
        $this->joinLeft('{users}_ignors', 'g', "g.ignored_user_id = i.contact_id AND g.user_id = '{$user_id}'");

        $this->filterEqual('user_id', $user_id);
        $this->filterEqual('contact_id', $contact_id);

        $this->groupBy('i.id');

        return $this->getItem('{users}_contacts', function($item, $model){
            $item['privacy_options'] = cmsModel::yamlToArray($item['privacy_options']);
            return $item;
        });

    }

    public function isContactExists($user_id, $contact_id){

        $this->select('u.id', 'id');
        $this->join('{users}', 'u', 'u.id = i.contact_id');

        $this->filterEqual('user_id', $user_id);
        $this->filterEqual('contact_id', $contact_id);

        $this->limit(1);

        $is_exists = (bool)$this->getCount('{users}_contacts');

        $this->resetFilters();

        return $is_exists;

    }

    public function deleteContact($user_id, $contact_id){

        $this->filterEqual('user_id', $user_id);
        $this->filterEqual('contact_id', $contact_id);
        $this->limit(1);

        return $this->deleteFiltered('{users}_contacts');

    }

//============================================================================//
//============================================================================//

    public function isContactIgnored($user_id, $contact_id){

        $this->filterEqual('user_id', $user_id);
        $this->filterEqual('ignored_user_id', $contact_id);
        $this->limit(1);

        $is_ignored = $this->getCount('{users}_ignors');

        $this->resetFilters();

        return $is_ignored;

    }

    public function ignoreContact($user_id, $contact_id){

        return $this->insert('{users}_ignors', array(
            'user_id' => $user_id,
            'ignored_user_id' => $contact_id
        ));

    }

    public function forgiveContact($user_id, $contact_id){

        $this->filterEqual('user_id', $user_id);
        $this->filterEqual('ignored_user_id', $contact_id);
        $this->limit(1);

        return $this->deleteFiltered('{users}_ignors');

    }

//============================================================================//
//============================================================================//

    public function addMessage($from_id, $recipients, $content){

        $message_ids = array();

        foreach($recipients as $to_id){

            $message_ids[] = $this->insert('{users}_messages', array(
                'from_id' => $from_id,
                'to_id'   => $to_id,
                'content' => $content
            ));

        }

        return sizeof($message_ids)>1 ? $message_ids : $message_ids[0];

    }

    public function deleteMessages($user_id, $ids){

        $this->filterEqual('from_id', $user_id);
        $this->filterIn('id', $ids);

        $this->lockFilters()->updateFiltered('{users}_messages', array(
           'is_deleted'  => 1,
           'date_delete' => NULL
        ));

        $this->filterEqual('is_new', 1);

        $delete_msg_ids = $this->selectOnly('id')->get('{users}_messages', function($item, $model){
            return $item['id'];
        }, false);

        $this->unlockFilters();

        if($delete_msg_ids){
            $this->deleteFiltered('{users}_messages');
        }

        $this->resetFilters();

        return $delete_msg_ids;

    }

    public function restoreMessages($user_id, $id){

        $this->filterEqual('from_id', $user_id);
        $this->filterEqual('id', $id);

        return $this->updateFiltered('{users}_messages', array(
           'is_deleted'  => null,
           'date_delete' => false
        ));

    }

//============================================================================//
//============================================================================//

    public function getMessage($id){

        $this->select('u.nickname', 'user_nickname');
        $this->select('u.avatar', 'user_avatar');
        $this->join('{users}', 'u', 'u.id = i.from_id');

        return $this->getItemById('{users}_messages', $id, function($item, $model){

            $item['user'] = array(
                'id'       => $item['from_id'],
                'nickname' => $item['user_nickname'],
                'avatar'   => $item['user_avatar']
            );

            return $item;

        });

    }

    public function getMessages($user_id, $contact_id){

        $this->select('u.nickname', 'user_nickname');
        $this->select('u.avatar', 'user_avatar');
        $this->join('{users}', 'u', 'u.id = i.from_id');

        if ($this->filter_on) { $this->filterAnd(); }

        $this->filterIn('from_id', array($user_id, $contact_id));
        $this->filterIn('to_id', array($user_id, $contact_id));
        $this->filterIsNull('is_deleted');

        $this->orderBy('id', 'desc');

        $messages = $this->get('{users}_messages', function($item, $model){

            $item['user'] = array(
                'id'       => $item['from_id'],
                'nickname' => $item['user_nickname'],
                'avatar'   => $item['user_avatar']
            );

            return $item;

        }, false);

        return is_array($messages) ? array_reverse($messages) : array();

    }

    public function getMessagesFromContact($user_id, $contact_id){

        $this->select('u.nickname', 'user_nickname');
        $this->select('u.avatar', 'user_avatar');
        $this->join('{users}', 'u', 'u.id = i.from_id');

        if ($this->filter_on) { $this->filterAnd(); }

        $this->filterStart();
        $this->filterEqual('from_id', $contact_id);
        $this->filterEqual('to_id', $user_id);
        $this->filterEnd();
        $this->filterIsNull('is_deleted');

        $this->orderBy('id');

        $messages = $this->get('{users}_messages', function($item, $model){

            $item['user'] = array(
                'id'       => $item['from_id'],
                'nickname' => $item['user_nickname'],
                'avatar'   => $item['user_avatar']
            );

            return $item;

        }, false);

        return $messages;

    }

//============================================================================//
//============================================================================//

    public function deleteUserMessages($user_id){

        $this->delete('{users}_ignors', $user_id, 'user_id');
        $this->delete('{users}_messages', $user_id, 'from_id');
        $this->delete('{users}_messages', $user_id, 'to_id');
        $this->delete('{users}_notices', $user_id, 'user_id');
        $this->delete('{users}_contacts', $user_id, 'user_id');
        $this->delete('{users}_contacts', $user_id, 'contact_id');

    }

//============================================================================//
//============================================================================//

    public function getNewMessagesCount($user_id){

        $this->filterEqual('to_id', $user_id);
        $this->filterEqual('is_new', 1);
        $this->filterIsNull('is_deleted');

        $count = $this->getCount('{users}_messages');

        $this->resetFilters();

        return $count;

    }

//============================================================================//
//============================================================================//

    public function setMessagesReaded($user_id, $contact_id){

        $this->filterEqual('from_id', $contact_id);
        $this->filterEqual('to_id', $user_id);

        $this->updateFiltered('{users}_messages', array(
           'is_new' => 0
        ));

    }

//============================================================================//
//============================================================================//

    public function addNotice($recipients, $notice){

        $notice_ids = array();

        foreach($recipients as $recipient){

			$id = is_array($recipient) ? $recipient['id'] : $recipient;

            $notice_ids[] = $this->insert('{users}_notices', array(
                'user_id' => $id,
                'content' => $notice['content'],
                'options' => isset($notice['options']) ? $notice['options'] : null,
                'actions' => isset($notice['actions']) ? $notice['actions'] : null,
            ));

        }

        return sizeof($notice_ids)>1 ? $notice_ids : $notice_ids[0];

    }

    public function deleteNotice($id){

        return $this->delete('{users}_notices', $id);

    }

    public function deleteUserNotices($user_id){

         $this->filterEqual('user_id', $user_id);

        return $this->deleteFiltered('{users}_notices');

    }

//============================================================================//
//============================================================================//

    public function getNoticesCount($user_id){

        $this->filterEqual('user_id', $user_id);

        return $this->getCount('{users}_notices');

    }

    public function getNotices($user_id){

        $this->orderBy('date_pub', 'desc');

        $this->filterEqual('user_id', $user_id);

        return $this->get('{users}_notices', function($item, $model){

            $item['options'] = cmsModel::yamlToArray($item['options']);
            $item['actions'] = cmsModel::yamlToArray($item['actions']);

            $item['options'] = array_merge($model->getDefaultNoticeOptions(), $item['options']);

            return $item;

        });

    }

    public function getNotice($id){

        return $this->getItemById('{users}_notices', $id, function($item, $model){

            $item['options'] = cmsModel::yamlToArray($item['options']);
            $item['actions'] = cmsModel::yamlToArray($item['actions']);

            $item['options'] = array_merge($model->getDefaultNoticeOptions(), $item['options']);

            return $item;

        });

    }

//============================================================================//
//============================================================================//

}
