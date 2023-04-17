<?php
/**
 * @property \modelBackendContent $model_backend_content
 */
class actionAdminCtypesRelations extends cmsAction {

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

        $this->table_name = 'content_relations';
        $this->grid_name  = 'ctype_relations';
        $this->title      = LANG_CP_CTYPE_RELATIONS;

        $this->tool_buttons = [
            [
                'class' => 'add',
                'title' => LANG_CP_RELATION_ADD,
                'href'  => $this->cms_template->href_to('ctypes', ['relations_add', $this->ctype['id']])
            ],
            [
                'class' => 'view_list',
                'title' => LANG_CP_CTYPE_TO_LIST,
                'href'  => $this->cms_template->href_to('ctypes')
            ]
        ];

        $this->list_callback = function ($model) {

            return $model->filterEqual('ctype_id', $this->ctype['id']);
        };
    }

    public function renderListItemsGrid(){

        // Для того, чтобы сформировалось подменю типа контента, см system/controllers/admin/actions/ctypes.php
        $this->dispatchEvent('ctype_loaded', [$this->ctype, 'relations']);

        $this->cms_template->addMenuItem('breadcrumb-menu', [
            'title' => LANG_HELP,
            'url'   => LANG_HELP_URL_CTYPES_RELATIONS,
            'options' => [
                'target' => '_blank',
                'icon' => 'question-circle'
            ]
        ]);

        return $this->traitRenderListItemsGrid();
    }

}
