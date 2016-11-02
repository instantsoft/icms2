<?php

class onCommentsAdminDashboardBlock extends cmsAction {

	public function run(){

        $show_count = 5;

        $this->model->disableApprovedFilter();

        $this->model->filterNotEqual('is_approved', 1);

        $total = $this->model->getCommentsCount();
        if(!$total){ $this->model->resetFilters(); return false; }

        $this->model->orderBy('date_pub', 'desc')->limit($show_count);

        $items = $this->model->getComments();

        $html = $this->cms_template->renderInternal($this, 'backend/admin_dashboard_block', array(
            'items' => $items,
            'show_count' => $show_count,
            'total' => $total
        ));

        return array(
            'title' => sprintf(LANG_COMMENTS_MODERATE_TITLE, $total),
            'html'  => $html
        );

    }

}
