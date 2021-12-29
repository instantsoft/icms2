<?php
/**
 * @property \modelWall $model
 */
class actionWallDelete extends cmsAction {

    public function run() {

        if (!$this->request->isAjax()) {
            cmsCore::error404();
        }

        $entry_id = $this->request->get('id', 0);

        if (!$entry_id) {
            return $this->cms_template->renderJSON([
                'error'   => true,
                'message' => LANG_ERROR
            ]);
        }

        $entry = $this->model->getEntry($entry_id);
        if (!$entry) {
            return $this->cms_template->renderJSON([
                'error'   => true,
                'message' => LANG_ERROR
            ]);
        }

        $entry = cmsEventsManager::hook('wall_before_delete', $entry);

        $controller = cmsCore::getController($entry['controller']);

        // Получаем права доступа
        $permissions = $controller->runHook('wall_permissions', [
            'profile_type' => $entry['profile_type'],
            'profile_id'   => $entry['profile_id']
        ]);

        if (!$permissions || !is_array($permissions)) {
            return $this->cms_template->renderJSON([
                'error'   => true,
                'message' => LANG_ERROR
            ]);
        }

        $is_wall_delete = false;

        if(isset($permissions['delete'])){
            $is_wall_delete = ($entry['user']['id'] == $this->cms_user->id) || !empty($permissions['delete']);
        }

        if(isset($permissions['delete_handler'])){
            $is_wall_delete = $permissions['delete_handler']($entry);
        }

        if (!$is_wall_delete) {
            return $this->cms_template->renderJSON([
                'error'   => true,
                'message' => LANG_ERROR
            ]);
        }

        $this->model->deleteEntry($entry_id);

        cmsEventsManager::hook('wall_after_delete', $entry);

        return $this->cms_template->renderJSON([
            'error'   => false,
            'message' => LANG_WALL_ENTRY_DELETED
        ]);
    }

}
