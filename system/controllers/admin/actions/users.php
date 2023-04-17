<?php
/**
 * @property \modelUsers $model_users
 */
class actionAdminUsers extends cmsAction {

    use icms\traits\controllers\actions\listgrid {
        getListItemsGridHtml as private traitGetListItemsGridHtml;
    }

    public function __construct($controller, $params = []) {

        parent::__construct($controller, $params);

        $this->table_name = '{users}';
        $this->grid_name  = 'users';

        $this->external_action_prefix = 'users_';

        $this->toolbar_hook = 'admin_users_toolbar';

        $this->tool_buttons = [
            [
                'class' => 'menu d-xl-none',
                'data'  => [
                    'toggle' =>'quickview',
                    'toggle-element' => '#left-quickview'
                ],
                'title' => LANG_GROUPS
            ],
            [
                'class' => 'users_add',
                'icon'  => 'user-plus',
                'title' => LANG_CP_USER_ADD,
                'href'  => null
            ],
            [
                'class' => 'users_group',
                'icon'  => 'users',
                'childs_count' => 3,
                'title' => LANG_GROUP,
                'href'  => ''
            ],
            [
                'icon'  => 'folder-plus',
                'level' => 2,
                'title' => LANG_CP_USER_GROUP_ADD,
                'href'  => $this->cms_template->href_to('users', 'group_add')
            ],
            [
                'class' => 'edit_user',
                'icon'  => 'pen',
                'level' => 2,
                'title' => LANG_CP_USER_GROUP_EDIT,
                'href'  => null
            ],
            [
                'class' => 'delete_user',
                'icon'  => 'times',
                'level' => 2,
                'title' => LANG_CP_USER_GROUP_DELETE,
                'href'  => null,
                'onclick' => "return confirm('".LANG_CP_USER_GROUP_DELETE_CONFIRM."')"
            ],
            [
                'class' => 'group_perms d-none',
                'icon'  => 'user-shield',
                'title' => LANG_CP_USER_GROUP_PERMS,
                'href'  => null
            ]
        ];

        $group_id = $params[0] ?? ltrim(cmsUser::getCookie('users_tree_path'), '/');

        if (!preg_match('/^([0-9]+)$/i', $group_id)) {
            $group_id = 0;
        }

        $this->list_callback = function ($model) use ($group_id) {

            if ($group_id) {
                $model->join('{users}_groups_members', 'm', "m.user_id = i.id AND m.group_id = '{$group_id}'");
            }

            $model->joinSessionsOnline('i');

            return $model;
        };
    }

    public function getListItemsGridHtml() {

        $groups = $this->model_users->getGroups();
        $groups = array_pad($groups, (count($groups) + 1) * -1, ['id' => 0, 'title' => LANG_ALL]);

        $grid_html = $this->traitGetListItemsGridHtml();

        return $this->cms_template->renderInternal($this, 'users', [
            'groups'    => $groups,
            'grid_html' => $grid_html
        ]);
    }

}
