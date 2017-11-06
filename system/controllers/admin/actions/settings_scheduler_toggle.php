<?php

class actionAdminSettingsSchedulerToggle extends cmsAction {

    public function run($id=false){

        if (!$id) {
            return $this->cms_template->renderJSON(array(
                'error' => true
            ));
		}

        $task = $this->model->getSchedulerTask($id);
        if (!$task) {
            return $this->cms_template->renderJSON(array(
                'error' => true
            ));
		}

        $is_active = $task['is_active'] ? 0 : 1;

        $this->model->toggleSchedulerPublication($id, $is_active);

        return $this->cms_template->renderJSON(array(
            'error' => false,
            'is_on' => $is_active
        ));

    }

}
