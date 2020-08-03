<?php

class actionContentWidgetDatasetsAjax extends cmsAction {

    public function run() {

        if (!$this->request->isAjax() || !cmsUser::isAdmin()) {
            return cmsCore::error404();
        }

        $ctype_id = $this->request->get('value', '');
        if (!$ctype_id) {
            return $this->cms_template->renderJSON(['0' => '']);
        }

        $target_controller = 'content';

        if (strpos($ctype_id, ':') !== false) {
            list($target_controller, $ctype_id) = explode(':', $ctype_id);
        }

        $datasets = $this->model->getContentDatasets($target_controller == 'content' ? $ctype_id : $target_controller);

        $list = [];

        if ($datasets) {
            $list[] = ['title' => '', 'value' => '0'];
            foreach ($datasets as $dataset) {
                $list[] = ['title' => $dataset['title'], 'value' => $dataset['id']];
            }
        }

        return $this->cms_template->renderJSON($list);
    }

}
