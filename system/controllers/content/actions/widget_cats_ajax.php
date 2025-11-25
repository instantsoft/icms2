<?php

class actionContentWidgetCatsAjax extends cmsAction {

    public function run() {

        if (!$this->request->isAjax() || !$this->cms_user->is_admin) {
            return cmsCore::error404();
        }

        $empty_title = $this->request->get('empty_title', '');

        $list = [['title' => strip_tags($empty_title), 'value' => '']];

        $ctype_id = $this->request->get('value', '');
        if (!$ctype_id) {
            return $this->cms_template->renderJSON($list);
        }

        if (is_numeric($ctype_id)) {
            $ctype = $this->model->getContentType($ctype_id);
        } else {
            $ctype = $this->model->getContentTypeByName($ctype_id);
        }

        if (!$ctype) {
            return $this->cms_template->renderJSON($list);
        }

        $cats = $this->model->getCategoriesTree($ctype['name']);

        if ($cats) {
            foreach ($cats as $cat) {

                if ($cat['ns_level'] > 1) {
                    $cat['title'] = str_repeat('-', $cat['ns_level']) . ' ' . $cat['title'];
                }

                $list[] = ['title' => $cat['title'].($cat['is_hidden'] ? ' ⚡️' : ''), 'value' => $cat['id']];
            }
        }

        return $this->cms_template->renderJSON($list);
    }

}
