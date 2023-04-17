<?php
/**
 * @property \modelBackendContent $model_backend_content
 */
class actionAdminContent extends cmsAction {

    use icms\traits\oneable;

    use icms\traits\controllers\actions\listgrid {
        getListItemsGridHtml as private traitGetListItemsGridHtml;
    }

    private $ctype = [];

    private $tree_path_key = '';

    private $category_id = 1;

    public function __construct($controller, $params = []) {

        parent::__construct($controller, $params);

        $this->external_action_prefix = 'content_';

        $this->toolbar_hook = 'admin_content_toolbar';
    }

    public function prepareRun() {

        $this->loadCtype(($this->params[0] ?? 0), ($this->params[1] ?? false));

        $category = $this->model_backend_content->getCategory($this->ctype['name'], $this->category_id);
        if (!$category) {
            return cmsCore::error404();
        }

        $this->table_name = $this->model->getContentTypeTableName($this->ctype['name']);
        $this->grid_name  = 'content_items';
        $this->grid_args  = [$this->ctype];
        $this->ups_key    = $this->ctype['name'].'.';

        $this->tool_buttons = [
            [
                'class' => 'menu d-xl-none',
                'data'  => [
                    'toggle' =>'quickview',
                    'toggle-element' => '#left-quickview'
                ],
                'title' => LANG_MENU
            ],
            [
                'class' => 'settings',
                'title' => LANG_CONFIG,
                'href'  => null
            ],
            [
                'class' => 'logs',
                'title' => LANG_MODERATION_LOGS,
                'href'  => null
            ],
            [
                'class'        => 'folder',
                'childs_count' => 4,
                'title'        => LANG_CATEGORIES
            ],
            [
                'class' => 'add_folder',
                'level' => 2,
                'title' => LANG_CP_CONTENT_CATS_ADD
            ],
            [
                'class' => 'edit_folder',
                'level' => 2,
                'title' => LANG_EDIT
            ],
            [
                'class'   => 'delete_folder',
                'level'   => 2,
                'title'   => LANG_DELETE_CATEGORY,
                'confirm' => LANG_DELETE_CATEGORY_CONFIRM
            ],
            [
                'class'   => 'tree_folder',
                'level'   => 2,
                'title'   => LANG_CP_CONTENT_CATS_ORDER,
                'onclick' => 'return contentCatsReorder($(this))'
            ],
            [
                'class' => 'add add_site',
                'title' => LANG_CP_CONTENT_ITEM_ADD
            ],
            [
                'class' => 'add add_cpanel',
                'title' => LANG_CP_CONTENT_ITEM_ADD_CP
            ]
        ];

        $this->list_callback = function ($model) use($category) {

            $model->filterCategory($this->ctype['name'], $category, true, !empty($this->ctype['options']['is_cats_multi']));

            $model->joinUser();

            $model->joinLeft(
                'moderators_logs',
                'mlog',
                "mlog.target_id = i.id AND mlog.target_controller = 'content' AND mlog.target_subject = '{$this->ctype['name']}' AND mlog.date_expired IS NOT NULL"
            );
            $model->select('mlog.date_expired', 'trash_date_expired');

            $model->joinModerationsTasks($this->ctype['name']);

            return $model;
        };
    }

    public function getListItemsGridHtml() {

        $ctypes = $this->getOnce($this->model_backend_content)->getContentTypesFiltered();

        $grid_html = $this->traitGetListItemsGridHtml();

        return $this->cms_template->renderInternal($this, 'content', [
            'key_path'  => $this->tree_path_key,
            'ctype'     => $this->ctype,
            'ctypes'    => $ctypes,
            'grid_html' => $grid_html
        ]);
    }

    private function loadCtype($ctype_id, $category_id) {

        if ($category_id !== false) {
            $this->category_id = $category_id;
        }

        // Если передан из урл
        if ($ctype_id) {

            $this->ctype = $this->model_backend_content->getContentType($ctype_id);

            if (!$this->ctype) {
                return cmsCore::error404();
            }
        }

        // Сохранённый путь дерева
        $tree_path = cmsUser::getCookie('content_tree_path');

        if ($tree_path) {

            $tree_path = ltrim($tree_path, '/');

            if (preg_match('/^([0-9\/\.]+)$/i', $tree_path)) {

                $this->tree_path_key = $tree_path;

                $tree_keys = explode('/', $tree_path);

                $tree_key = explode('.', end($tree_keys));

                $ctype_id = (int)($tree_key[0] ?? 0);

                // если не передан из урла, берём из куки
                if (!$this->ctype) {
                    $this->ctype = $this->model_backend_content->getContentType($ctype_id);
                }

                if ($this->ctype) {

                    if ($category_id === false) {
                        $this->category_id = (int)($tree_key[1] ?? $this->category_id);
                    }

                    return;
                }
            }
        }

        // Иначе первый из списка
        if (!$this->ctype) {

            $ctypes = $this->getOnce($this->model_backend_content)->getContentTypesFiltered();

            $this->ctype = reset($ctypes);

            $this->tree_path_key = $this->ctype['id'].'.'.$this->category_id;
        }

        return;
    }

}
