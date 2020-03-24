<?php

class actionUsersIndex extends cmsAction {

    public function run($dataset_name = 'all'){

        if(!$this->listIsAllowed()){
            cmsCore::error404();
        }

        $datasets = $this->getDatasets();

        if(!$dataset_name || !isset($datasets[$dataset_name])){
            cmsCore::error404();
        }

        $dataset = $datasets[$dataset_name];

        if (isset($dataset['filter']) && is_callable($dataset['filter'])){
            $this->model = $dataset['filter']($this->model, $dataset);
        }

        if (!empty($datasets[$dataset_name]['order'])){
            $this->model->orderBy( $datasets[$dataset_name]['order'][0], $datasets[$dataset_name]['order'][1] );
        }

        // Формируем базовые URL для страниц
        $page_url = array(
            'base'  => href_to($this->name, $dataset_name ? 'index/'.$dataset_name : ''),
            'first' => href_to($this->name, $dataset_name ? 'index/'.$dataset_name : '')
        );

        return $this->cms_template->render('index', array(
            'page_title'         => ($dataset_name != 'all' ? LANG_USERS . ' - ' . $dataset['title'] : LANG_USERS),
            'base_ds_url'        => href_to($this->name, 'index').'%s',
            'datasets'           => $datasets,
            'dataset_name'       => $dataset_name,
            'dataset'            => $dataset,
            'user'               => $this->cms_user,
            'profiles_list_html' => $this->renderProfilesList($page_url, $dataset_name)
        ), $this->request);

    }

}
