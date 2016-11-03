<?php

class onModerationAdminDashboardBlock extends cmsAction {

	public function run(){

        $show_count = 5;

        $total = $this->model->getTasksCount();
        if(!$total){ $this->model->resetFilters(); return false; }

        $this->model->orderBy('date_pub', 'desc')->limit($show_count);

        $content = cmsCore::getModel('content');

        $_items = $this->model->getTasks();

        $items = array();

        foreach ($_items as $item) {

            $ctype = $content->getContentTypeByName($item['ctype_name']);

            $item['ctype_title'] = $ctype['title'];

            $items[] = $item;

        }

        $html = $this->cms_template->renderInternal($this, 'backend/admin_dashboard_block', array(
            'items'      => $items,
            'show_count' => $show_count,
            'content'    => $content,
            'total'      => $total
        ));

        return array(
            'title' => sprintf(LANG_MODERATION_TITLE, $total),
            'html'  => $html
        );

    }

}
