<?php

class actionAdminWidgetsPageContentCats extends cmsAction {

    public function run() {

        if (!$this->request->isAjax() ||
                !($ctype_name = $this->request->get('value', ''))
        ) {
            cmsCore::error404();
        }

        $content_model = cmsCore::getModel('content');

        $ctype = $content_model->getContentTypeByName($ctype_name);
        if (!$ctype) { cmsCore::error404(); }

        $tree = $content_model->limit(0)->getCategoriesTree($ctype['name'], false) ?: [];

        $items = [];

        foreach ($tree as $item) {
            $items[($ctype['name'] . '/' . $item['slug'])] = str_repeat('- ', $item['ns_level']) . ' ' . $item['title'];
        }

        return $this->cms_template->renderJSON($items);
    }

}
