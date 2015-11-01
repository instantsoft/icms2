<?php

class actionAdminContentItemsAjax extends cmsAction {

    public function run($ctype_id, $parent_id){

        if (!$this->request->isAjax()) { cmsCore::error404(); }

        $content_model = cmsCore::getModel('content');

        $ctype = $content_model->getContentType($ctype_id);
        if(!$ctype){$this->halt();}
        $category = $content_model->getCategory($ctype['name'], $parent_id);
        if(!$category){$this->halt();}

        $grid = $this->loadDataGrid('content_items', $ctype['name']);

        $filter     = array();
        $filter_str = $this->request->get('filter');

        // Для сохранения настроек грида необходимо добавить такую строку со своим ключом
        $filter_str = cmsUser::getUPSActual('admin.filter_str.'.$ctype['name'], $filter_str);

        if($filter_str){

            parse_str($filter_str, $filter);

            if (!empty($filter['advanced_filter'])){

                parse_str($filter['advanced_filter'], $dataset_filters);

                if (!empty($dataset_filters['dataset'])){
                    $dataset_id = $dataset_filters['dataset'];
                    $dataset = $content_model->getContentDataset($dataset_id);
                    $content_model->applyDatasetFilters($dataset, true);
                }

                $content_model->applyDatasetFilters($dataset_filters);

            }

            $content_model->applyGridFilter($grid, $filter);

			// В случае обновления 'columns' грида для заполнения полей фильтров
            $grid['filter'] = $filter;

        }

        $content_model->filterCategory($ctype['name'], $category, $ctype['is_cats_recursive']);

        $content_model->disableApprovedFilter();
        $content_model->disablePubFilter();

        $total = $content_model->getContentItemsCount($ctype['name']);

        $perpage = isset($filter['perpage']) ? $filter['perpage'] : admin::perpage;

        $pages = ceil($total / $perpage);

        $content_model->setPerPage($perpage);

        $items = $content_model->getContentItems($ctype['name']);

        cmsTemplate::getInstance()->renderGridRowsJSON($grid, $items, $total, $pages);

        $this->halt();

    }

}