<?php
/**
 * @property \modelContent $model_content
 */
class actionAdminWidgetsPageAutocomplete extends cmsAction {

    public function run() {

        if (!$this->request->isAjax() ||
            !($term       = $this->request->get('term', '')) ||
            !($ctype_name = $this->request->get('ctype', ''))
        ) {
            return cmsCore::error404();
        }

        $ctype = $this->model_content->getContentTypeByName($ctype_name);
        if (!$ctype) {
            cmsCore::error404();
        }

        $this->model_content->filterStart()->
                filterLike('title', "{$term}%")->filterOr()->
                filterLike('slug', "{$term}%")->filterEnd();

        $items = $this->model_content->get($this->model_content->table_prefix . $ctype['name']) ?: [];

        $result = [];

        $ctype_default = cmsConfig::get('ctype_default') ?? [];

        foreach ($items as $item) {

            $result[] = [
                'id'    => $item['id'],
                'label' => $item['title'],
                'value' => $ctype['name'] . '/' . $item['slug'] . '.html'
            ];
        }

        return $this->cms_template->renderJSON($result);
    }

}
