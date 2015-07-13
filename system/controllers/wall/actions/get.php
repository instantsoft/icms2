<?php

class actionWallGet extends cmsAction {

    public function run(){

        if (!$this->request->isAjax()){ cmsCore::error404(); }

        $entry_id = $this->request->get('id');

        // Проверяем валидность
        $is_valid = is_numeric($entry_id);

        if (!$is_valid){
            $result = array('error' => true, 'message' => LANG_ERROR);
            cmsTemplate::getInstance()->renderJSON($result);
        }

        $user = cmsUser::getInstance();

        $entry = $this->model->getEntry($entry_id);

        if ($entry['user']['id'] != $user->id && !$user->is_admin){
            $result = array('error' => true, 'message' => LANG_ERROR);
            cmsTemplate::getInstance()->renderJSON($result);
        }

        // Формируем и возвращаем результат
        $result = array(
            'error' => $entry ? false : true,
            'id' => $entry_id,
            'html' => $entry ? string_strip_br($entry['content']) : false
        );

        cmsTemplate::getInstance()->renderJSON($result);

    }

}
