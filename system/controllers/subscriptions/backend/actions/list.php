<?php

class actionSubscriptionsList extends cmsAction {

    public function run(){

        $grid = $this->loadDataGrid('subscriptions');

        if ($this->request->isAjax()) {

            $filter     = array();
            $filter_str = $this->request->get('filter', '');

            $filter_str = cmsUser::getUPSActual('admin.grid_filter.subscriptions', $filter_str);

            if ($filter_str){
                parse_str($filter_str, $filter);
                $this->model->applyGridFilter($grid, $filter);
            }

            $total = $this->model->getCount('subscriptions');

            $perpage = isset($filter['perpage']) ? $filter['perpage'] : admin::perpage;

            $this->model->setPerPage($perpage);

            $pages = ceil($total / $perpage);

            $items = $this->model->get('subscriptions', function($item, $model) {

                $item['params'] = cmsModel::stringToArray($item['params']);

                return $item;

            });

            $items = cmsEventsManager::hook('admin_subscriptions_list', $items);

            $this->cms_template->renderGridRowsJSON($grid, $items, $total, $pages);

            $this->halt();

        }

        return $this->cms_template->render('backend/subscriptions', array(
            'grid' => $grid
        ));

    }

}
