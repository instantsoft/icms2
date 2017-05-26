<?php

class actionGroupsIndex extends cmsAction {

    public function run($tab=false){

        $dataset_name = false;
        $datasets = $this->getDatasets();

        if (!$tab){
            $tab = $this->options['is_ds_popular'] ? 'popular' : 'all';
        }

        if ($tab && isset($datasets[$tab])) {

            $dataset_name = $tab;
            $dataset = $datasets[$tab];

            if (isset($dataset['filter']) && is_callable($dataset['filter'])){
                $this->model = $dataset['filter']($this->model, $dataset);
            }

        } else if ($tab) { cmsCore::error404(); }

        // Сортировка
        if ($dataset_name && !empty($datasets[$dataset_name]['order'])){
            $this->model->orderBy( $datasets[$dataset_name]['order'][0], $datasets[$dataset_name]['order'][1] );
        }

        // Формируем базовые URL для страниц
        $page_url = array(
            'base'  => href_to($this->name, $dataset_name ? 'index/'.$dataset_name : ''),
            'first' => href_to($this->name, $dataset_name ? 'index/'.$dataset_name : '')
        );

        $this->cms_template->setPageTitle(LANG_GROUPS);
        $this->cms_template->addBreadcrumb(LANG_GROUPS, href_to('groups'));

        if (cmsUser::isAllowed('groups', 'add')) {
            $this->cms_template->addToolButton(array(
                'class' => 'add',
                'title' => LANG_GROUPS_ADD,
                'href'  => href_to('groups', 'add'),
            ));
        }

        if (cmsUser::isAdmin()){
            $this->cms_template->addToolButton(array(
                'class' => 'page_gear',
                'title' => LANG_GROUPS_SETTINGS,
                'href'  => href_to('admin', 'controllers', array('edit', 'groups'))
            ));
        }

        return $this->cms_template->render('index', array(
            'datasets'         => $datasets,
            'base_ds_url'      => href_to_rel('groups') . '/index/%s',
            'dataset_name'     => $dataset_name,
            'dataset'          => $dataset,
            'user'             => $this->cms_user,
            'groups_list_html' => $this->renderGroupsList($page_url, $dataset_name)
        ), $this->request);

    }

}
