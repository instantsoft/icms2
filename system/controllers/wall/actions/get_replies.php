<?php
/**
 * @property \modelWall $model
 */
class actionWallGetReplies extends cmsAction {

    public function run() {

        if (!$this->request->isAjax()) {
            cmsCore::error404();
        }

        $entry_id = $this->request->get('id', 0);

        if (!is_numeric($entry_id)) {
            return $this->cms_template->renderJSON(['error' => true, 'message' => LANG_ERROR]);
        }

        $entry = $this->model->getEntry($entry_id);

        if (!$entry) {
            return $this->cms_template->renderJSON(['error' => true, 'message' => LANG_ERROR]);
        }

        $controller = cmsCore::getController($entry['controller']);

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

        $this->model->
                filterEqual('parent_id', $entry_id)->
                orderBy('date_pub', 'asc');

        $replies = $this->model->getEntries($this->cms_user, $this->getWallEntryActions($permissions));

        if (!$replies) {
            return $this->cms_template->renderJSON(['error' => true, 'message' => LANG_ERROR]);
        }

        $replies = cmsEventsManager::hook('wall_before_list', $replies);

        $html = $this->cms_template->renderInternal($this, 'entry', [
            'entries'     => $replies,
            'user'        => $this->cms_user,
            'permissions' => $permissions
        ]);

        return $this->cms_template->renderJSON([
            'error' => false,
            'html'  => $html
        ]);
    }

}
