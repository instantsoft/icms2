<?php

class actionAdminContentItemsAjax extends cmsAction {

    public function run($ctype_id, $parent_id){

        if (!$this->request->isAjax()) { return cmsCore::error404(); }

        $content_model = cmsCore::getModel('content');

        $ctype = $content_model->getContentType($ctype_id);
        if(!$ctype){$this->halt();}
        $category = $content_model->getCategory($ctype['name'], $parent_id);
        if(!$category){$this->halt();}

        $grid = $this->loadDataGrid('content_items', $ctype['name']);

        $filter     = array();
        $filter_str = $this->request->get('filter', '');

        // Одновременно смениться и тип контента, и настройка diff_order не могут
        $diff_order = cmsUser::getUPS('admin.grid_filter.content.diff_order');

        // Изменён тип контента и должна быть сохранена сортировка
        if($filter_str && mb_strpos($filter_str, 'ctype_changed=1') !== false && $diff_order){
            // Проверим, что эта сортировка есть в бд, иначе будет использоваться пришедшая
            $ups_filter_str = cmsUser::getUPS('admin.grid_filter.content.'.$ctype['name']);
            if($ups_filter_str){
                $filter_str = $ups_filter_str;
            }
            // Чтобы заполнить поля поиска фильтра
            $grid['options']['load_columns'] = true;

        } else {
            $filter_str = cmsUser::getUPSActual('admin.grid_filter.content.'.$ctype['name'], $filter_str);
        }

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

                // Различная сортировка у разных типов контента, сохранение настройки
                $new_diff_order = !empty($dataset_filters['diff_order']) ? '1' : '0';
                if($new_diff_order !== $diff_order){
                    cmsUser::setUPS('admin.grid_filter.content.diff_order', $new_diff_order);
                }

            }

            if(!empty($filter['order_by']) && !isset($grid['columns'][$filter['order_by']])){
                unset($filter['order_by']);
            }

            $content_model->applyGridFilter($grid, $filter);

			// В случае обновления 'columns' грида для заполнения полей фильтров
            $grid['filter'] = $filter;

        }

        $content_model->filterCategory($ctype['name'], $category, true);

        $content_model->disableDeleteFilter();
        $content_model->disableApprovedFilter();
        $content_model->disablePubFilter();
        $content_model->disablePrivacyFilter();

        $total = $content_model->getContentItemsCount($ctype['name']);

        $perpage = isset($filter['perpage']) ? $filter['perpage'] : admin::perpage;

        $pages = ceil($total / $perpage);

        $content_model->setPerPage($perpage);

        $content_model->joinLeft(
                'moderators_logs',
                'mlog',
                "mlog.target_id = i.id AND mlog.target_controller = 'content' AND mlog.target_subject = '{$ctype['name']}' AND mlog.date_expired IS NOT NULL"
        );
        $content_model->select('mlog.date_expired', 'trash_date_expired');

        $content_model->joinModerationsTasks($ctype['name']);

        $items = $content_model->getContentItems($ctype['name']);

        $this->cms_template->renderGridRowsJSON($grid, $items, $total, $pages);

        $this->halt();

    }

}
