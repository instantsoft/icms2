<?php

class actionTagsIndex extends cmsAction {

    public function run($target = '', $tag_name = '') {

        // ничего нет - показываем список тегов
        if (!$target && !$tag_name) {
            return $this->displayTags();
        }

        // передан только $target, значит это тег
        if (!$tag_name) {
            $tag_name = $target;
            $target   = '';
        }

        // субъект в формате controller_name-subject_name
        if ($target && !preg_match('/^([a-z0-9\_]+\-{1}[a-z0-9\_]+)$/', $target)) {
            return cmsCore::error404();
        }

        $tag_name = urldecode($tag_name);

        // получаем тег
        $tag = $this->model->getTagByTag($tag_name);
        if (!$tag) {
            return cmsCore::error404();
        }

        // получаем все субъекты тега
        $targets = $this->model->getTagTargets($tag['id']);
        if (!$targets) {
            return cmsCore::error404();
        }

        $menu_items = cmsEventsManager::hookAll('tags_search_subjects', [$tag, $targets]);
        if (!$menu_items) {
            return cmsCore::error404();
        }

        // субъект по умолчанию - первый из списка
        if (!$target) {

            foreach ($menu_items as $controller_name => $_menu_items) {

                if (empty($_menu_items)) {
                    continue;
                }

                $first_subject = array_keys($_menu_items);

                $target = $first_subject[0];

                // редиректим на правильный урл
                return $this->redirect(href_to('tags', $target, string_urlencode($tag_name)), 301);
            }
        }

        list($target_controller, $target_subject) = explode('-', $target);

        if (!cmsCore::isControllerExists($target_controller) || !cmsController::enabled($target_controller)) {
            return cmsCore::error404();
        }

        $page_url = href_to($this->name, $target, string_urlencode($tag_name));

        // результат поиска получаем только по переданному контроллеру
        $controller = cmsCore::getController($target_controller, $this->request);

        $list_html = $controller->runHook('tags_search', [$target_subject, $tag, $page_url]);
        if (!$list_html) {
            return cmsCore::error404();
        }

        foreach ($menu_items as $menu_item) {
            $this->cms_template->addMenuItems('results_tabs', $menu_item);
        }

        $seo_title = sprintf(LANG_TAGS_SEARCH_BY_TAG, $tag['tag']);
        $seo_keys  = $seo_title;
        $seo_desc  = $seo_title;
        $seo_h1    = $seo_title;

        $seo_data = [
            'tag'         => $tag['tag'],
            'ctype_title' => $menu_items[$target_controller][$target]['title'] ?? null
        ];

        $seo_title_pattern = $tag['tag_title'] ?: get_localized_value('seo_title_pattern', $this->options);
        $seo_desc_pattern  = $tag['tag_desc'] ?: get_localized_value('seo_desc_pattern', $this->options);
        $seo_h1_pattern    = $tag['tag_h1'] ?: get_localized_value('seo_h1_pattern', $this->options);

        if ($seo_title_pattern) {
            $seo_title = string_replace_keys_values_extended($seo_title_pattern, $seo_data);
        }

        if ($seo_desc_pattern) {
            $seo_desc = string_replace_keys_values_extended($seo_desc_pattern, $seo_data);
        }

        if ($seo_h1_pattern) {
            $seo_h1 = string_replace_keys_values_extended($seo_h1_pattern, $seo_data);
        }

        if ($tag['description']) {
            $tag['description'] = string_replace_keys_values_extended($tag['description'], $seo_data);
        }

        if ($this->cms_user->is_admin) {

            $this->cms_template->addToolButton([
                'class' => 'edit',
                'icon'  => 'edit',
                'title' => LANG_TAGS_TAG_EDIT,
                'href'  => href_to('admin', 'controllers', ['edit', 'tags', 'edit', $tag['id']])
            ]);

            $this->cms_template->addToolButton([
                'class' => 'page_gear',
                'icon'  => 'wrench',
                'title' => LANG_TAGS_SETTINGS,
                'href'  => href_to('admin', 'controllers', ['edit', 'tags'])
            ]);
        }

        return $this->cms_template->render('search', [
            'page'      => $this->request->get('page', 1),
            'target'    => $target,
            'tag'       => $tag,
            'seo_title' => $seo_title,
            'seo_keys'  => $seo_keys,
            'seo_desc'  => $seo_desc,
            'seo_h1'    => $seo_h1,
            'html'      => $list_html
        ]);
    }

    private function displayTags() {

        if ($this->cms_user->is_admin) {
            $this->cms_template->addToolButton([
                'class' => 'page_gear',
                'icon'  => 'wrench',
                'title' => LANG_TAGS_SETTINGS,
                'href'  => href_to('admin', 'controllers', ['edit', 'tags'])
            ]);
        }

        $this->cms_template->addHead('<link rel="canonical" href="' . href_to_abs($this->name) . '">');

        return $this->cms_template->render('tags', $this->getTagsWidgetParams($this->options));
    }

}
