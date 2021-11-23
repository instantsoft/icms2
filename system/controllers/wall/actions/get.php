<?php
/**
 * @property \modelWall $model
 */
class actionWallGet extends cmsAction {

    public function run() {

        if (!$this->request->isAjax()) {
            cmsCore::error404();
        }

        $entry_id = $this->request->get('id', 0);

        if (!is_numeric($entry_id)) {
            return $this->cms_template->renderJSON(['error' => true, 'message' => LANG_ERROR]);
        }

        $entry = $this->model->getEntry($entry_id);

        if ($entry['user']['id'] != $this->cms_user->id && !$this->cms_user->is_admin) {
            return $this->cms_template->renderJSON(['error' => true, 'message' => LANG_ERROR]);
        }

        return $this->cms_template->renderJSON([
            'error' => $entry ? false : true,
            'id'    => $entry_id,
            'html'  => $entry ? string_strip_br($entry['content']) : false
        ]);
    }

}
