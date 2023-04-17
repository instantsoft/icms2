<?php
/**
 * @property \modelBackendContent $model_backend_content
 */
class actionAdminCtypesDatasets extends cmsAction {

    use icms\traits\controllers\actions\listgrid {
        renderListItemsGrid as private traitRenderListItemsGrid;
    }

    private $ctype = [];

    public function __construct($controller, $params = []) {

        parent::__construct($controller, $params);

        $ctype_id = $params[0] ?? 0;

        $this->ctype = $this->model_backend_content->getContentType($ctype_id);
        if (!$this->ctype) {
            return cmsCore::error404();
        }

        $this->table_name = 'content_datasets';
        $this->grid_name  = 'ctype_datasets';
        $this->grid_args  = [href_to($this->name, 'ctypes', ['datasets_edit', '{id}'])];
        $this->title      = LANG_CP_CTYPE_DATASETS.' · '.$this->ctype['title'];

        $this->tool_buttons = [
            [
                'class' => 'add',
                'title' => LANG_CP_DATASET_ADD,
                'href'  => $this->cms_template->href_to('ctypes', ['datasets_add', $this->ctype['id']])
            ],
            [
                'class' => 'view_list',
                'title' => LANG_CP_CTYPE_TO_LIST,
                'href'  => $this->cms_template->href_to('ctypes')
            ],
        ];

        $this->list_callback = function ($model) {

            $model->filterEqual('ctype_id', $this->ctype['id']);

            return $model;
        };

    }

    public function renderListItemsGrid(){

        $this->dispatchEvent('ctype_loaded', [$this->ctype, 'datasets']);

        $this->cms_template->addMenuItem('breadcrumb-menu', [
            'title' => LANG_HELP,
            'url'   => LANG_HELP_URL_CTYPES_DATASETS,
            'options' => [
                'target' => '_blank',
                'icon' => 'question-circle'
            ]
        ]);

        return $this->traitRenderListItemsGrid();
    }

}
