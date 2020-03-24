<?php

class modelAuth extends cmsModel {

    const RESUBMIT_TIME = 300;

    public function addInvites($user_id, $qty=1){

        $result = true;

        for ($i=1; $i<=$qty; $i++){

            $code = string_random();
            $code = strtoupper(substr($code, mt_rand(0, 16), 10));

            $result = $result &&
                        $this->insert('{users}_invites', array(
                            'user_id' => $user_id,
                            'code' => $code
                        ));

        }

        $this->filterEqual('id', $user_id)->increment('{users}', 'invites_count', $qty);

        $this->update('{users}', $user_id, array(
            'date_invites' => null
        ));

        cmsCache::getInstance()->clean('users.user.'.$user_id);

        return $result;

    }

    public function getUserInvites($user_id){

        return $this->filterEqual('user_id', $user_id)->get('{users}_invites', function($item, $model){

            $item['page_url'] = href_to_abs('auth', 'register') . "?inv={$item['code']}";

            return $item;

        });

    }

    public function getNextInvite($user_id){

        return $this->filterEqual('user_id', $user_id)->filterIsNull('email')->getItem('{users}_invites');

    }

    public function getInviteByCode($code){

        return $this->filterEqual('code', $code)->getItem('{users}_invites');

    }

    public function markInviteSended($invite_id, $user_id, $email){

        $this->update('{users}_invites', $invite_id, array(
            'email' => $email
        ));

        $this->filterEqual('id', $user_id)->decrement('{users}', 'invites_count', 1);

        cmsCache::getInstance()->clean('users.user.'.$user_id);

    }

    public function revokeInvites($user_id){

        $this->update('{users}', $user_id, array(
            'invites_count' => 0
        ));

        $this->delete('{users}_invites', $user_id, 'user_id');

        cmsCache::getInstance()->clean('users.user.'.$user_id);

    }

    public function deleteInvite($id){

        $this->delete('{users}_invites', $id);

    }

}
