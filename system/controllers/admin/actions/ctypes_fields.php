<?php
/**
 * @property \modelBackendContent $model_backend_content
 */
class actionAdminCtypesFields extends cmsAction {

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

        $this->table_name = $this->model->getContentTypeTableName($this->ctype['name']) . '_fields';
        $this->grid_name  = 'ctype_fields';
        $this->grid_args  = $this->ctype['name'];
        $this->title      = LANG_CP_CTYPE_FIELDS;

        $this->tool_buttons = [
            [
                'class' => 'add',
                'title' => LANG_CP_FIELD_ADD,
                'href'  => $this->cms_template->href_to('ctypes', ['fields_add', $this->ctype['id']])
            ],
            [
                'class' => 'view_list',
                'title' => LANG_CP_CTYPE_TO_LIST,
                'href'  => $this->cms_template->href_to('ctypes')
            ]
        ];

        $this->list_callback = function ($model) {

            $model->selectTranslatedField('i.values', $this->table_name, 'default');

            return $model;
        };

        $this->item_callback = function ($item, $model) {

            $field_class = 'field' . string_to_camel('_', $item['type']);

            $handler = new $field_class($item['name']);

            $item['handler_title'] = $handler->getTitle();

            return $item;
        };
    }

    public function renderListItemsGrid(){

        // Для того, чтобы сформировалось подменю типа контента, см system/controllers/admin/actions/ctypes.php
        $this->dispatchEvent('ctype_loaded', [$this->ctype, 'fields']);

        $this->cms_template->addMenuItem('breadcrumb-menu', [
            'title' => LANG_HELP,
            'url'   => LANG_HELP_URL_CTYPES_FIELDS,
            'options' => [
                'target' => '_blank',
                'icon'   => 'question-circle'
            ]
        ]);

        return $this->traitRenderListItemsGrid();
    }

}
