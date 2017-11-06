<?php

class actionAdminUsersAutocomplete extends cmsAction {

    public function run(){

        if (!$this->request->isAjax()) { cmsCore::error404(); }

        $term = $this->request->get('term', '');
        if (!$term) { cmsCore::error404(); }

        $users = cmsCore::getModel('users')->filterLike('email', "{$term}%")->getUsers();

        $result = array();

        if ($users){
            foreach($users as $user){

                $result[] = array(
                    'id'    => $user['id'],
                    'label' => $user['nickname'],
                    'value' => $user['email']
                );

            }
        }

        return $this->cms_template->renderJSON($result);

    }

}
