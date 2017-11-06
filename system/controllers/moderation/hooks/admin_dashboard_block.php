<?php

class onModerationAdminDashboardBlock extends cmsAction {

    private $content_model;
    private $show_count = 5;

    public function __construct($controller, $params=array()){

        parent::__construct($controller, $params);

        $this->content_model = cmsCore::getModel('content');

    }

	public function run(){

        $moderation_data = $this->getModerationData();

        $moderation_trash_data = $this->getModerationTrashData();

        if(!$moderation_data && !$moderation_trash_data){
            return false;
        }

        $dashboard_blocks = array();

        if($moderation_data){
            $dashboard_blocks[] = array(
                'title' => sprintf(LANG_MODERATION_TITLE, $moderation_data['total']),
                'html'  => $moderation_data['html']
            );
        }

        if($moderation_trash_data){
            $dashboard_blocks[] = array(
                'title' => sprintf(LANG_MODERATION_CLEAR_TITLE, $moderation_trash_data['total']),
                'html'  => $moderation_trash_data['html']
            );
        }

        return $dashboard_blocks;

    }

    private function getModerationTrashData() {

        $this->content_model->filterNotNull('date_expired');
        $this->content_model->filterEqual('i.action', modelModeration::LOG_TRASH_ACTION);

        $total = $this->content_model->getCount('moderators_logs');
        if(!$total){ $this->content_model->resetFilters(); return false; }

        $this->content_model->orderBy('date_expired', 'asc')->limit($this->show_count);

        $logs = $this->content_model->get('moderators_logs', function ($item, $model){
                $item['data'] = cmsModel::yamlToArray($item['data']);
                if($item['target_controller'] == 'content'){
                    $ctype = $model->getContentTypeByName($item['target_subject']);
                    $item['subject_title'] = $ctype['title'];
                }
                return $item;
        });

        $html = $this->cms_template->renderInternal($this, 'backend/admin_dashboard_trash_block', array(
            'logs'       => $logs,
            'show_count' => $this->show_count,
            'total'      => $total
        ));

        return array(
            'total' => $total,
            'html'  => $html
        );

    }

    private function getModerationData() {

        $total = $this->model->getTasksCount();
        if(!$total){ $this->model->resetFilters(); return false; }

        $this->model->orderBy('date_pub', 'desc')->limit($this->show_count);

        $_items = $this->model->getTasks();

        $items = array();

        foreach ($_items as $item) {

            $ctype = $this->content_model->getContentTypeByName($item['ctype_name']);

            $item['ctype_title'] = $ctype['title'];

            $items[] = $item;

        }

        $html = $this->cms_template->renderInternal($this, 'backend/admin_dashboard_block', array(
            'items'      => $items,
            'show_count' => $this->show_count,
            'total'      => $total
        ));

        return array(
            'total' => $total,
            'html'  => $html
        );

    }

}
