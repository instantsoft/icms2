<?php

class actionAdminUsersAutocomplete extends cmsAction {

    public function run() {

        if (!$this->request->isAjax()) {
            return cmsCore::error404();
        }

        $term = $this->request->get('term', '');
        if (!$term) {
            return cmsCore::error404();
        }

        $users = cmsCore::getModel('users')->filterLike('email', "{$term}%")->getUsers();

        $result = [];

        if ($users) {
            foreach ($users as $user) {
                $result[] = [
                    'id'    => $user['id'],
                    'label' => $user['nickname'],
                    'value' => $user['email']
                ];
            }
        }

        return $this->cms_template->renderJSON($result);
    }

}
