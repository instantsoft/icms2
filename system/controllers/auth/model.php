<?php

class modelAuth extends cmsModel{

    public function addInvites($user_id, $qty=1){

        $result = true;

        for ($i=1; $i<=$qty; $i++){

            $code = md5(md5(implode(',', array($user_id, microtime(true), rand(0,10000), session_id()))));
            $code = mb_strtoupper(mb_substr($code, rand(0, 16), 10));

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

        return $result;

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

    }

    public function deleteInvite($id){

        $this->delete('{users}_invites', $id);

    }

}
