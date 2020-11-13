<?php

class actionContentWidgetRelationsAjax extends cmsAction {

    public function run() {

        if (!$this->request->isAjax() || !cmsUser::isAdmin()) {
            return cmsCore::error404();
        }

        $list = ['0' => ''];

        $ctype_id = $this->request->get('value', 0);
        if (!$ctype_id) {
            return $this->cms_template->renderJSON($list);
        }

        $ctype = $this->model->getContentType($ctype_id);
        if (!$ctype) {
            return $this->cms_template->renderJSON($list);
        }

        $parents = $this->model->getContentTypeParents($ctype_id);

        if ($parents) {
            foreach ($parents as $parent) {
                $list[] = ['title' => $ctype['title'] . ' > ' . $parent['ctype_title'], 'value' => $parent['id']];
            }
        }

        return $this->cms_template->renderJSON($list);
    }

}
