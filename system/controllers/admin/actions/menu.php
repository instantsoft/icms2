<?php

class actionAdminMenu extends cmsAction {

    use icms\traits\controllers\actions\listgrid {
        getListItemsGridHtml as private traitGetListItemsGridHtml;
    }

    public function __construct($controller, $params = []) {

        parent::__construct($controller, $params);

        $this->table_name = 'menu_items';
        $this->grid_name  = 'menu_items';

        $this->external_action_prefix = 'menu_';

        $this->toolbar_hook = 'admin_menu_toolbar';

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
                'class' => 'add_item',
                'icon'  => 'plus-circle',
                'title' => LANG_CP_MENU_ITEM_ADD,
                'href'  => $this->cms_template->href_to('menu', ['item_add', 1, 0])
            ],
            [
                'class' => 'add_menu',
                'icon'  => 'folder-plus',
                'title' => LANG_CP_MENU_ADD,
                'href'  => $this->cms_template->href_to('menu', ['add'])
            ],
            [
                'class' => 'edit_menu',
                'icon'  => 'edit',
                'title' => LANG_CP_MENU_EDIT,
                'href'  => $this->cms_template->href_to('menu', ['edit'])
            ],
            [
                'class' => 'delete_menu',
                'icon'  => 'minus-circle',
                'title' => LANG_CP_MENU_DELETE,
                'confirm' => LANG_CP_MENU_DELETE_CONFIRM,
                'href'  => $this->cms_template->href_to('menu', ['delete'])
            ]
        ];

        $tree_key = $params[0] ?? ltrim(cmsUser::getCookie('menu_tree_path'), '/');

        if (!preg_match('/^([0-9\.]+)$/i', $tree_key)) {
            $tree_key = '1.0';
        }

        $tree_key = explode('.', $tree_key);

        $menu_id   = $tree_key[0] ?? 1;
        $parent_id = $tree_key[1] ?? 0;

        $this->list_callback = function ($model) use ($menu_id, $parent_id) {

            $model->filterEqual('menu_id', $menu_id);

            $model->filterEqual('parent_id', $parent_id);

            $model->limit(false);

            return $model;
        };
    }

    public function getListItemsGridHtml() {

        $menus = $this->model_menu->getMenus();

        $grid_html = $this->traitGetListItemsGridHtml();

        return $this->cms_template->renderInternal($this, 'menu', [
            'menus'     => $menus,
            'grid_html' => $grid_html
        ]);
    }

}
