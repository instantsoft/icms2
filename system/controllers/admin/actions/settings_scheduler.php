<?php

class actionAdminSettingsScheduler extends cmsAction {

    use icms\traits\controllers\actions\listgrid {
        renderListItemsGrid as private traitRenderListItemsGrid;
    }

    public function __construct($controller, $params = []) {

        parent::__construct($controller, $params);

        $this->table_name = 'scheduler_tasks';
        $this->grid_name  = 'scheduler';
        $this->title      = LANG_CP_SCHEDULER;

        $this->external_action_prefix = 'settings_scheduler_';

        $this->tool_buttons = [
            [
                'class' => 'add',
                'title' => LANG_CP_SCHEDULER_TASK_ADD,
                'href'  => $this->cms_template->href_to('settings', ['scheduler', 'add'])
            ]
        ];

        $this->cms_template->addBreadcrumb(LANG_CP_SECTION_SETTINGS, $this->cms_template->href_to('settings'));
        $this->cms_template->addBreadcrumb(LANG_CP_SCHEDULER, $this->cms_template->href_to('settings', ['scheduler']));

        $this->cms_template->addMenuItems('admin_toolbar', $this->getSettingsMenu());
    }

    public function renderListItemsGrid(){

        $this->cms_template->addMenuItem('breadcrumb-menu', [
            'title' => LANG_HELP,
            'url'   => LANG_HELP_URL_SETTINGS_SCHEDULER,
            'options' => [
                'target' => '_blank',
                'icon' => 'question-circle'
            ]
        ]);

        return $this->traitRenderListItemsGrid();
    }

}
