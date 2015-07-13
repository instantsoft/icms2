<?php

class actionWallDelete extends cmsAction {

    public function run(){

        if (!$this->request->isAjax()){ cmsCore::error404(); }

        $entry_id = $this->request->get('id');

        // Проверяем валидность
        $is_valid = is_numeric($entry_id);

        if (!$is_valid){
            $result = array(
                'error' => true,
                'message' => LANG_ERROR
            );
            cmsTemplate::getInstance()->renderJSON($result);
        }

        $user = cmsUser::getInstance();

        $entry = $this->model->getEntry($entry_id);

        $this->model->deleteEntry($entry_id);

        $result = array(
            'error' => false,
            'message' => LANG_WALL_ENTRY_DELETED
        );

        cmsTemplate::getInstance()->renderJSON($result);

    }

}
