<?php

class actionUsersIndex extends cmsAction {

    public function run($dataset_name = '') {

        if (!$this->listIsAllowed()) {
            return cmsCore::error404();
        }

        $datasets = $this->getDatasets();

        if (!isset($datasets[$dataset_name])) {
            return cmsCore::error404();
        }

        $dataset = $datasets[$dataset_name];

        if (isset($dataset['filter']) && is_callable($dataset['filter'])) {
            $this->model = $dataset['filter']($this->model, $dataset);
        }

        // Формируем базовые URL для страниц
        $page_url = [
            'base'  => href_to($this->name, $dataset_name ? $dataset_name : ''),
            'first' => href_to($this->name, $dataset_name ? $dataset_name : '')
        ];

        $this->cms_template->addHead('<link rel="canonical" href="' . href_to_abs('users', (!$dataset_name ? null : $dataset_name)) . '">');

        return $this->cms_template->render('index', [
            'page_title'         => ($dataset_name ? LANG_USERS . ' - ' . $dataset['title'] : LANG_USERS),
            'base_ds_url'        => href_to($this->name) . '%s',
            'datasets'           => $datasets,
            'dataset_name'       => $dataset_name,
            'dataset'            => $dataset,
            'user'               => $this->cms_user,
            'profiles_list_html' => $this->renderProfilesList($page_url, $dataset_name)
        ], $this->request);
    }

}
