<?php
/**
 * @property \modelBackendContent $model_backend_content
 */
class actionAdminCtypesFilters extends cmsAction {

    use icms\traits\controllers\actions\listgrid {
        getListItemsGridHtml as private traitgetListItemsGridHtml;
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

        $this->table_name = $this->model->getContentTypeTableName($this->ctype['name']) . '_filters';
        $this->grid_name  = 'ctype_filters';
        $this->grid_args  = ['ctype' => $this->ctype];
        $this->title      = LANG_CP_CTYPE_FILTERS;

        $this->tool_buttons = [
            [
                'class' => 'add',
                'title' => LANG_CP_FILTER_ADD,
                'href'  => $this->cms_template->href_to('ctypes', ['filters_add', $this->ctype['id']])
            ],
            [
                'class' => 'view_list',
                'title' => LANG_CP_CTYPE_TO_LIST,
                'href'  => $this->cms_template->href_to('ctypes')
            ]
        ];
    }

    public function getListItemsGridHtml(){

        if (!$this->model_backend_content->isFiltersTableExists($this->ctype['name'])) {

            return $this->cms_template->renderInternal($this, 'ctypes_filters_error', [
                'ctype' => $this->ctype
            ]);
        }

        return $this->traitgetListItemsGridHtml();
    }

    public function renderListItemsGrid() {

        // Для того, чтобы сформировалось подменю типа контента, см system/controllers/admin/actions/ctypes.php
        $this->dispatchEvent('ctype_loaded', [$this->ctype, 'filters']);

        $this->cms_template->addMenuItem('breadcrumb-menu', [
            'title' => LANG_HELP,
            'url'   => LANG_HELP_URL_CTYPES_FILTERS,
            'options' => [
                'target' => '_blank',
                'icon' => 'question-circle'
            ]
        ]);

        return $this->traitRenderListItemsGrid();
    }

}
