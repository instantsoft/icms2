<?php
/**
 * @property \modelBackendContent $model_backend_content
 */
class actionAdminCtypesProps extends cmsAction {

    use icms\traits\controllers\actions\listgrid {
        getListItemsGridHtml as private traitGetListItemsGridHtml;
    }

    private $ctype = [];
    private $cookie_path_key = '';
    private $tree_path_key = '';

    public function __construct($controller, $params = []) {

        parent::__construct($controller, $params);

        $ctype_id = $params[0] ?? 0;

        $this->ctype = $this->model_backend_content->getContentType($ctype_id);
        if (!$this->ctype) {
            return cmsCore::error404();
        }

        $this->table_name = $this->model->getContentTypeTableName($this->ctype['name']) . '_props_bind';
        $this->grid_name  = 'ctype_props';
        $this->grid_args  = $this->cms_template->href_to('ctypes', ['props_reorder', $this->ctype['name']]);

        $this->tool_buttons = [
            [
                'class' => 'menu d-xl-none',
                'data'  => [
                    'toggle' =>'quickview',
                    'toggle-element' => '#left-quickview'
                ],
                'title' => LANG_CATEGORIES
            ],
            [
                'class' => 'add d-none datagrid_change',
                'title' => LANG_CP_FIELD_ADD,
                'data'  => [
                    'href' => $this->cms_template->href_to('ctypes', ['props_add', $this->ctype['id']])
                ]
            ],
            [
                'class' => 'add_folder datagrid_change',
                'title' => LANG_CP_CONTENT_CATS_ADD,
                'data'  => [
                    'href'  => $this->cms_template->href_to('content', ['cats_add', $this->ctype['id']])
                ]
            ],
            [
                'class' => 'edit_folder d-none datagrid_change',
                'title' => LANG_CP_CONTENT_CATS_EDIT,
                'data'  => [
                    'href'  => $this->cms_template->href_to('content', ['cats_edit', $this->ctype['id']])
                ]
            ],
            [
                'class' => 'delete_folder d-none datagrid_change datagrid_csrf',
                'title' => LANG_DELETE_CATEGORY,
                'data'  => [
                    'href'  => $this->cms_template->href_to('content', ['cats_delete', $this->ctype['id']])
                ],
                'confirm' => LANG_DELETE_CATEGORY_CONFIRM
            ]
        ];

        $this->cookie_path_key = 'props_'.$this->ctype['name'].'_tree_path';
        $this->tree_path_key   = $this->ctype['id'].'.0';

        $default_category_id = 0;

        if (cmsUser::hasCookie($this->cookie_path_key)) {

            $tree_key = ltrim(cmsUser::getCookie($this->cookie_path_key), '/');

            if (preg_match('/^([0-9\/\.]+)$/i', $tree_key)) {

                $this->tree_path_key = $tree_key;

                $tree_keys = explode('/', $tree_key);

                $tree_key = explode('.', end($tree_keys));

                $default_category_id = $tree_key[1] ?? 0;
            }
        }

        $category_id = $params[1] ?? $default_category_id;

        $this->list_callback = function ($model) use($category_id) {

            $model->selectOnly('p.*');
            $model->select('p.id', 'prop_id');
            $model->select('i.id', 'id');
            $model->select('i.cat_id', 'cat_id');

            $model->join($model->table_prefix . $this->ctype['name'] . '_props', 'p', 'p.id = i.prop_id');

            if ($category_id) {
                $model->filterEqual('i.cat_id', $category_id);
            }

            $model->groupBy('i.prop_id');

            $model->limit(false);

            return $model;
        };
    }

    public function getListItemsGridHtml() {

        // Для того, чтобы сформировалось подменю типа контента, см system/controllers/admin/actions/ctypes.php
        $this->dispatchEvent('ctype_loaded', [$this->ctype, 'props']);

        $cats = $this->model_backend_content->getSubCategories($this->ctype['name']);

        $props = $this->model_backend_content->orderBy('title')->getContentProps($this->ctype['name']);

        $grid_html = $this->traitGetListItemsGridHtml();

        return $this->cms_template->renderInternal($this, 'ctypes_props', [
            'cookie_path_key' => $this->cookie_path_key,
            'tree_path_key'   => $this->tree_path_key,
            'ctype'           => $this->ctype,
            'cats'            => $cats,
            'props'           => $props,
            'grid_html'       => $grid_html
        ]);
    }

}
