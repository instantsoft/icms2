<?php

class actionWallSubmit extends cmsAction {

    public function run(){

        if (!$this->request->isAjax()){ cmsCore::error404(); }

        $action = $this->request->get('action', '');

        $csrf_token      = $this->request->get('csrf_token', '');
        $controller_name = $this->request->get('pc', '');
        $profile_type    = $this->request->get('pt', '');
        $profile_id      = $this->request->get('pi', '');
        $parent_id       = $this->request->get('parent_id', '');
        $entry_id        = $this->request->get('id', '');
        $content         = $this->request->get('content', '');

        // Проверяем валидность
        $is_valid = $controller_name && $profile_type && $action &&
                    ($this->validate_sysname($controller_name)===true) &&
                    ($this->validate_sysname($profile_type)===true) &&
                    is_numeric($profile_id) &&
                    is_numeric($parent_id) &&
                    (!$entry_id || is_numeric($entry_id)) &&
                    cmsForm::validateCSRFToken($csrf_token, false) &&
                    in_array($action, array('add', 'preview', 'update'));

        if (!$is_valid){ return $this->error(); }

        if(!cmsCore::isControllerExists($controller_name)){
            return $this->error();
        }

        // какой контроллер обслуживаем
        $controller = cmsCore::getController($controller_name);

        //
        // Получаем права доступа
        //
        $permissions = $controller->runHook('wall_permissions', array(
            'profile_type' => $profile_type,
            'profile_id'   => $profile_id
        ));

        if (!$permissions || !is_array($permissions)){ return $this->error(); }

        $editor_params = cmsCore::getController('wysiwygs')->getEditorParams([
            'editor'  => $this->options['editor'],
            'presets' => $this->options['editor_presets']
        ]);

        // Типографируем текст
        $content_html = cmsEventsManager::hook('html_filter', [
            'text'         => $content,
            'is_auto_br'   => (!$editor_params['editor'] || $editor_params['editor'] == 'markitup'),
            'build_smiles' => $editor_params['editor'] == 'markitup'
        ]);

        if($this->validate_required($content_html) !== true){
            return $this->error(ERR_VALIDATE_REQUIRED);
        }

        //
        // Превью записи
        //
        if ($action=='preview'){

            return $this->cms_template->renderJSON(array(
                'error' => false,
                'html'  => cmsEventsManager::hook('parse_text', $content_html)
            ));

        }

        //
        // Редактирование записи
        //
        if ($action=='update'){

            $entry = $this->model->getEntry($entry_id);

            if ($entry['user']['id'] != $this->cms_user->id && !$this->cms_user->is_admin){ $this->error(); }

            list($entry_id, $content, $content_html) = cmsEventsManager::hook('wall_before_update', array($entry_id, $content, $content_html));

            $this->model->updateEntryContent($entry_id, $content, $content_html);

            $entry_html = cmsEventsManager::hook('parse_text', $content_html);

        }

        //
        // Добавление записи
        //
        if ($action=='add'){

            // проверяем права на добавление
            if(!$parent_id){
                if (empty($permissions['add'])){ return $this->error(); }
            } else {
                if (empty($permissions['reply'])){ return $this->error(); }
            }

            // Собираем данные записи
            $entry = array(
                'user_id'      => $this->cms_user->id,
                'parent_id'    => $parent_id,
                'controller'   => $controller_name,
                'profile_type' => $profile_type,
                'profile_id'   => $profile_id,
                'content'      => $content,
                'content_html' => $content_html
            );

            // Сохраняем запись
            $entry_id = $this->model->addEntry(cmsEventsManager::hook('wall_before_add', $entry));

            if ($entry_id){

                // Получаем и рендерим добавленную запись
                $entry = $this->model->getEntry($entry_id);

                $entry['content_html'] = cmsEventsManager::hook('parse_text', $entry['content_html']);

                $entry_html = $this->cms_template->renderInternal($this, 'entry', array(
                    'entries'     => array($entry),
                    'user'        => $this->cms_user,
                    'permissions' => $permissions
                ));

                // действия после добавления
                $controller->runHook('wall_after_add', array(
                    'profile_type' => $profile_type,
                    'profile_id'   => $profile_id,
                    'entry'        => $entry,
                    'wall_model'   => $this->model
                ));

            }

        }

        // Формируем и возвращаем результат
        $result = array(
            'error'     => $entry_id ? false : true,
            'message'   => $entry_id ? LANG_WALL_ENTRY_SUCCESS : LANG_WALL_ENTRY_ERROR,
            'id'        => $entry_id,
            'parent_id' => isset($entry['parent_id']) ? $entry['parent_id'] : 0,
            'html'      => isset($entry_html) ? $entry_html : false
        );

        return $this->cms_template->renderJSON($result);

    }

    private function error($message=LANG_WALL_ENTRY_ERROR){

        return $this->cms_template->renderJSON(array('error' => true, 'message' => $message));

    }

}
