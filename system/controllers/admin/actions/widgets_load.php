<?php
/**
 * @property \modelBackendWidgets $model_backend_widgets
 */
class actionAdminWidgetsLoad extends cmsAction {

    public function run() {

        if (!$this->request->isAjax()) {
            return cmsCore::error404();
        }

        cmsCore::loadAllControllersLanguages();

        $page_id  = $this->request->get('page_id', 0);
        $template = $this->request->get('template', '');

        $tpls = cmsCore::getTemplates();
        if (!$template || !in_array($template, $tpls)) {
            $template = cmsConfig::get('template');
        }

        $page_ids = [0];

        if ($page_id) {

            $page_ids[] = $page_id;

            $page = $this->model_backend_widgets->getPage($page_id);
            if ($page) {
                // имитируем uri
                if ($page['url_mask']) {

                    $page['url_mask'] = explode("\n", $page['url_mask']);

                    $uri = str_replace([
                        '%', '*', '{slug}'
                    ], [
                        '000', 'ab/c_', 'abc-0'
                    ], trim($page['url_mask'][0]));

                    $matched_pages = $this->cms_core->detectMatchedWidgetPages($this->cms_core->getWidgetsPages(), $uri);

                    if ($matched_pages) {
                        foreach (array_keys($matched_pages) as $pid) {
                            $page_ids[] = $pid;
                        }
                        $page_ids = array_unique($page_ids);
                    }
                }
            }
        }

        $scheme = $this->model_backend_widgets->getWidgetBindingsScheme($page_id, $page_ids, $template);

        return $this->cms_template->renderJSON([
            'is_exists' => ($scheme !== false),
            'scheme'    => $scheme
        ]);
    }

}
