<?php

class onActivityAdminDashboardBlock extends cmsAction {

	public function run($options){

        if(!empty($options['only_titles'])){

            return [
                'activity' => LANG_ACTIVITY
            ];

        }

        $dashboard_blocks = [];

        if(!array_key_exists('activity', $options['dashboard_enabled']) || !empty($options['dashboard_enabled']['activity'])){

            $this->model->limit(50);

            $items = $this->model->getEntries();

            $dashboard_blocks[] = array(
                'title' => LANG_ACTIVITY,
                'name' => 'activity',
                'html'  => $this->cms_template->renderInternal($this, 'backend/admin_dashboard_block_activity', array(
                    'items' => $items
                ))
            );

        }

        return $dashboard_blocks;

    }

}
