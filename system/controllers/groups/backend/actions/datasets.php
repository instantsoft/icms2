<?php

class actionGroupsDatasets extends cmsAction {

    use icms\traits\controllers\actions\listgrid {
        run as private traitRun;
    }

    public function __construct($controller, $params = []) {

        parent::__construct($controller, $params);

        $this->table_name = 'content_datasets';
        $this->grid_name  = 'ctype_datasets';
        $this->grid_args  = [$this->cms_template->href_to('datasets', ['edit', '{id}'])];
        $this->title      = LANG_CP_CTYPE_DATASETS;

        $this->tool_buttons = [
            [
                'class' => 'add',
                'title' => LANG_CP_DATASET_ADD,
                'href'  => $this->cms_template->href_to('datasets', 'add')
            ],
            [
                'class'  => 'help',
                'title'  => LANG_HELP,
                'target' => '_blank',
                'href'   => LANG_HELP_URL_CTYPES_DATASETS
            ],
        ];

        $this->list_callback = function ($model) {

            $model->filterEqual('target_controller', 'groups');

            return $model;
        };

    }

    public function run($do = false, $id = 0){

        $admin = cmsCore::getController('admin', $this->request);

        // для добавления/редактирования вызываем экшены админки
        if ($do){

            $this->cms_template->setContext($admin);

            $html = $admin->runExternalAction('ctypes_datasets_'.$do, ($do === 'add' ? [$this->name] : [$id]));

            $this->cms_template->restoreContext();

            return $html;
        }

        // Меняем контекст контроллера для экшена
        $this->controller = $admin;

        return $this->traitRun();
    }

}
