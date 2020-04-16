<?php

class onContentUserPrivacyTypes extends cmsAction {

    public function run(){

        $types = array();

        $ctypes = $this->model->getContentTypes();

        foreach ($ctypes as $ctype) {
            // проверяем наличие доступа
            if (!cmsUser::isAllowed($ctype['name'], 'add') || !isset($ctype['labels']['many'])) { continue; }

            $types['view_user_'.$ctype['name']] = array(
                'title'   => sprintf(LANG_USERS_PRIVACY_PROFILE_CTYPE, $ctype['labels']['many']),
                'options' => array('anyone', 'friends')
            );

        }

        return $types;

    }

}
