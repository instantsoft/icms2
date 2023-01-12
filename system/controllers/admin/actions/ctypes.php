<?php

class actionAdminCtypes extends cmsAction {

    use icms\traits\controllers\actions\listgrid {
        renderListItemsGrid as private traitRenderListItemsGrid;
    }

    public function __construct($controller, $params = []) {

        parent::__construct($controller, $params);

        $this->table_name = 'content_types';
        $this->grid_name  = 'ctypes';
        $this->title      = LANG_CP_SECTION_CTYPES;

        $this->external_action_prefix = 'ctypes_';

        $this->tool_buttons = [
            [
                'class' => 'add',
                'title' => LANG_CP_CTYPES_ADD,
                'href'  => $this->cms_template->href_to('ctypes', ['add'])
            ]
        ];

        $this->list_callback = function ($model) {

            $model->orderBy('ordering', 'asc');

            return $model;
        };

        $this->addEventListener('ctype_loaded', function($controller, $ctype, $do){

            $this->cms_template->addBreadcrumb(LANG_CP_SECTION_CTYPES, $this->cms_template->href_to('ctypes'));

            if(!empty($ctype['id'])){
                $this->cms_template->addBreadcrumb($ctype['title'], $this->cms_template->href_to('ctypes', ['edit', $ctype['id']]));
            }

            $this->cms_template->addMenuItems('admin_toolbar', $controller->getCtypeMenu($do, (!empty($ctype['id']) ? $ctype['id'] : null)));

        });
    }

    public function renderListItemsGrid(){

        $this->cms_template->addMenuItem('breadcrumb-menu', [
            'title' => LANG_HELP,
            'url'   => LANG_HELP_URL_CTYPES,
            'options' => [
                'target' => '_blank',
                'icon' => 'question-circle'
            ]
        ]);

        return $this->traitRenderListItemsGrid();
    }

}
