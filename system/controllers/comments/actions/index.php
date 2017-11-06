<?php

class actionCommentsIndex extends cmsAction{

    public function run($dataset_name = 'all'){

        if(!empty($this->options['disable_icms_comments'])){
            cmsCore::error404();
        }

        $datasets = $this->getDatasets();

        if(!$dataset_name || !isset($datasets[$dataset_name])){
            cmsCore::error404();
        }

        $dataset = $datasets[$dataset_name];

        if (isset($dataset['filter']) && is_callable($dataset['filter'])){
            $this->model = $dataset['filter']( $this->model );
        }

        // Формируем базовые URL для страниц
        $page_url = array(
            'base'  => href_to($this->name, $dataset_name),
            'first' => href_to($this->name, $dataset_name)
        );

        // Получаем HTML списка комментариев
        $items_list_html = $this->renderCommentsList($page_url, $dataset_name);

        $rss_link = '';
        if ($this->isControllerEnabled('rss') && $dataset_name == 'all' && $this->model->isRssFeedEnable()){
            $rss_link = href_to('rss', 'feed', 'comments');
        }

        return $this->cms_template->render('index', array(
            'page_title'      => ($dataset_name != 'all' ? LANG_COMMENTS . ' - ' . $dataset['title'] : LANG_COMMENTS),
            'base_ds_url'     => href_to($this->name).'%s',
            'rss_link'        => $rss_link,
            'datasets'        => $datasets,
            'dataset_name'    => $dataset_name,
            'dataset'         => $dataset,
            'user'            => $this->cms_user,
            'items_list_html' => $items_list_html
        ), $this->request);

    }

}
