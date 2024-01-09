<?php

class onGroupsContentPrivacyTypes extends cmsAction {

    public function run($data) {

        list($ctype, $fields, $action, $item) = $data;

        $group_id = $this->cms_core->request->get('group_id', 0);

        if (!empty($group_id) || !empty($item['parent_id'])) {

            $group_id = $group_id ? $group_id : $item['parent_id'];

            $group = $this->model->getGroup($group_id);
            if (!$group) {
                return false;
            }

            $types = [
                3 => LANG_PRIVACY_GROUPS,
                4 => sprintf(LANG_PRIVACY_GROUPS_ADD, $ctype['labels']['many'])
            ];

            $privacy_field = false;

            if (!empty($group['roles'])) {

                $field_name = 'allow_groups_roles';
                $table_name = $this->model->getContentTypeTableName($ctype['name']);

                if (!$this->model->db->isFieldExists($table_name, $field_name)) {
                    $this->model->db->query("ALTER TABLE `{#}{$table_name}` ADD `{$field_name}` VARCHAR(200) NULL DEFAULT NULL");
                }

                $roles = $group['roles'];

                $privacy_field = [
                    new fieldList($field_name, [
                        'title' => LANG_GROUPS_EDIT_ROLES,
                        'is_multiple' => true,
                        'visible_depend' => ['is_private' => ['show' => ['5']]],
                        'generator' => function ($group) use ($roles) {
                            $items = [];
                            foreach ($roles as $role_id => $role) {
                                $items[$role_id] = $role;
                            }
                            return $items;
                        }
                    ])
                ];

                $types[5] = LANG_PRIVACY_GROUPS_ROLES;
            }

            return [
                'name'   => $this->name,
                'fields' => $privacy_field,
                'types'  => $types
            ];
        }

        return false;
    }

}
