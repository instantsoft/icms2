<?php
/**
 * @property \modelContent $model
 */
class actionContentFolderDelete extends cmsAction {

    public function run() {

        $id = $this->request->get('id', 0);
        if (!$id) { return cmsCore::error404(); }

        $folder = $this->model->getContentFolder($id);
        if (!$folder) { return cmsCore::error404(); }

        if (($folder['user_id'] != $this->cms_user->id) && !$this->cms_user->is_admin) {
            return cmsCore::error404();
        }

        $ctype = $this->model->getContentType($folder['ctype_id']);

        $this->model->deleteContentFolder($folder);

        $this->redirect(href_to_profile($folder['user'], ['content', $ctype['name']]));
    }

}
