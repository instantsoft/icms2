<?php

class actionAdminControllers extends cmsAction {

    use icms\traits\controllers\actions\listgrid {
        renderListItemsGrid as private traitRenderListItemsGrid;
    }

    public function __construct($controller, $params = []) {

        parent::__construct($controller, $params);

        $this->table_name = 'controllers';
        $this->grid_name  = 'controllers';
        $this->title      = LANG_CP_SECTION_CONTROLLERS;

        $this->external_action_prefix = 'controllers_';

        $this->list_callback = function ($model) {

            cmsCore::loadAllControllersLanguages();

            if (!$model->order_by) {
                $model->orderByList([
                    [
                        'by' => 'is_enabled',
                        'to' => 'desc'
                    ],
                    [
                        'by' => 'title',
                        'to' => 'asc'
                    ]
                ]);
            }

            return $model;
        };

        $this->item_callback = function ($item, $model) {

            $item['title'] = string_lang($item['name'] . '_CONTROLLER', $item['title']);

            return $item;
        };
    }

    public function renderListItemsGrid(){

        $this->cms_template->addMenuItems('admin_toolbar', $this->getAddonsMenu());

        $this->cms_template->addMenuItem('breadcrumb-menu', [
            'title' => LANG_HELP,
            'url'   => LANG_HELP_URL_COMPONENTS,
            'options' => [
                'target' => '_blank',
                'icon' => 'question-circle'
            ]
        ]);

        return $this->traitRenderListItemsGrid();
    }

}
