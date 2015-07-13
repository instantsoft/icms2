<?php

class actionAdminUsersAutocomplete extends cmsAction {

    public function run(){

        if (!$this->request->isAjax()) { cmsCore::error404(); }

        $term = $this->request->get('term');

        if (!$term) { cmsCore::error404(); }

        $users_model = cmsCore::getModel('users');

        $users = $users_model->filterLike("nickname", "{$term}%")->getUsers();

        $result = array();

        if ($users){
            foreach($users as $user){

                $result[] = array(
                    'id' => $user['id'],
                    'label' => $user['nickname'],
                    'value' => $user['nickname']
                );

            }
        }

        cmsTemplate::getInstance()->renderJSON($result);

    }

}
