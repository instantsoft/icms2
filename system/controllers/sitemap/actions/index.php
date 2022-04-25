<?php

class actionSitemapIndex extends cmsAction {

    public function run($page = 'sitemap') {

        if (!$page) {
            return cmsCore::error404();
        }

        if (!preg_match('/^([a-z0-9_]*)$/', $page)) {
            return cmsCore::error404();
        }

        $file_path = $this->cms_config->root_path . 'cache/static/sitemaps/' . $page . '.json';

        if (!is_readable($file_path)) {
            return cmsCore::error404();
        }

        $this->cms_template->addBreadcrumb(LANG_SITEMAP_HTML);
        $this->cms_template->setPageTitle(LANG_SITEMAP_HTML);
        $this->cms_template->addHead('<meta name="robots" content="noindex, follow, noarchive">');

        return $this->cms_template->render('index', [
            'show_back' => ($page !== 'sitemap'),
            'items'     => json_decode(file_get_contents($file_path), true)
        ]);
    }

}
