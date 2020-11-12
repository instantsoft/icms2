<?php

class actionContentItemBindList extends cmsAction {

    public function run() {

        $ctype_name       = $this->request->get('ctype_name', '');
        $child_ctype_name = $this->request->get('child_ctype_name', '');
        $item_id          = $this->request->get('id', 0);
        $authors          = $this->request->get('authors', '');
        $field            = $this->request->get('field', '');
        $text             = $this->request->get('text', '');
        $mode             = $this->request->get('mode', 'childs');
        $selected_ids     = $this->request->get('selected', '');
        $input_action     = $this->request->get('input_action', 'bind');

        if (!$ctype_name || !$child_ctype_name || !$authors || !$field) {
            return cmsCore::error404();
        }

        if ($this->validate_sysname($ctype_name) !== true || $this->validate_sysname($child_ctype_name) !== true) {
            return cmsCore::error404();
        }

        $ctype = $this->model->getContentTypeByName($ctype_name);
        if (!$ctype) { return cmsCore::error404(); }

        // дочерний контроллер
        $target_controller = 'content';

        $child_ctype = $this->model->getContentTypeByName($child_ctype_name);
        // если нет, проверяем по контроллеру
        if (!$child_ctype) {
            if (cmsCore::isControllerExists($child_ctype) && cmsController::enabled($child_ctype)) {

                if ($mode != 'parents') {
                    return cmsCore::error404();
                }

                $child_ctype = [
                    'name' => $child_ctype_name,
                    'id'   => null
                ];

                $target_controller = $child_ctype_name;
            } else {
                return cmsCore::error404();
            }
        }

        $relation = $this->model->getContentRelationByTypes($ctype['id'], $child_ctype['id'], $target_controller);
        if (!$relation) { return cmsCore::error404(); }

        $is_allowed_to_add    = cmsUser::isAllowed($child_ctype_name, 'add_to_parent');
        $is_allowed_to_bind   = $input_action == 'select' || cmsUser::isAllowed($child_ctype_name, 'bind_to_parent');
        $is_allowed_to_unbind = cmsUser::isAllowed($child_ctype_name, 'bind_off_parent');

        if ($mode != 'unbind' && (!$is_allowed_to_add && !$is_allowed_to_bind)) {
            return cmsCore::error404();
        }

        if ($mode == 'unbind' && !$is_allowed_to_unbind) {
            return cmsCore::error404();
        }

        if ($text) {

            if ($mode == 'childs' || $mode == 'unbind') {

                if ($relation['target_controller'] != 'content') {
                    $this->model->setTablePrefix('');
                }

                $fields = $this->model->getContentFields($child_ctype_name);

                $this->model->setTablePrefix(cmsModel::DEFAULT_TABLE_PREFIX);

            } else if ($mode == 'parents') {

                $fields = $this->model->getContentFields($ctype_name);
            }

            if (!$fields) {
                return cmsCore::error404();
            }

            $fields = cmsEventsManager::hook('ctype_content_fields', $fields);

            $filter_fields = ['id'];

            foreach ($fields as $_field) {
                if ($_field['handler']->filter_type == 'str') {
                    $filter_fields[] = $_field['name'];
                }
            }

            if (!in_array($field, $filter_fields)) {
                return cmsCore::error404();
            }

            $this->model->filterLike($field, "%{$text}%");
        }

        $this->model->limit(10);

        $perm = cmsUser::getPermissionValue($child_ctype_name, 'bind_to_parent');

        if ($this->cms_user->is_admin) {
            $perm = 'all_to_all';
        }

        if ($selected_ids) {
            $ids = [];
            foreach (explode(',', $selected_ids) as $id) {
                if (!is_numeric($id)) {
                    continue;
                }
                $ids[] = trim($id);
            }
            if ($ids) {
                $this->model->filterNotIn('id', $ids);
            }
        }

        if ($mode == 'childs') {

            if ($perm == 'own_to_own' || $perm == 'own_to_all' || $authors == 'own') {
                $this->model->filterEqual('user_id', $this->cms_user->id);
            }

            if ($perm == 'other_to_own' || $perm == 'other_to_other' || $perm == 'other_to_all') {
                $this->model->filterNotEqual('user_id', $this->cms_user->id);
            }

            if ($item_id) {
                $join_condition = "r.parent_ctype_id = '{$ctype['id']}' AND " .
                        "r.parent_item_id = '{$item_id}' AND " .
                        'r.child_ctype_id ' . ($child_ctype['id'] ? '=' . $child_ctype['id'] : 'IS NULL' ) . ' AND ' .
                        "r.child_item_id = i.id AND r.target_controller = '{$target_controller}'";

                $this->model->joinLeft('content_relations_bind', 'r', $join_condition);
                $this->model->filterIsNull('r.id');
            }

            $total = $this->model->getContentItemsCount($child_ctype_name);
            $items = $this->model->getContentItems($child_ctype_name);
        }

        if ($mode == 'parents') {

            if (in_array($perm, ['own_to_own', 'all_to_own', 'other_to_own']) || $authors == 'own') {
                $this->model->filterEqual('user_id', $this->cms_user->id);
            }

            if (in_array($perm, ['own_to_other', 'all_to_other', 'other_to_other'])) {
                $this->model->filterNotEqual('user_id', $this->cms_user->id);
            }

            if ($item_id) {
                $join_condition = "r.parent_ctype_id = '{$ctype['id']}' AND " .
                        'r.parent_item_id = i.id AND ' .
                        'r.child_ctype_id ' . ($child_ctype['id'] ? '=' . $child_ctype['id'] : 'IS NULL' ) . ' AND ' .
                        "r.child_item_id = '{$item_id}' AND r.target_controller = '{$target_controller}'";

                $this->model->joinLeft('content_relations_bind', 'r', $join_condition);
                $this->model->filterIsNull('r.id');
            }

            $total = $this->model->getContentItemsCount($ctype_name);
            $items = $this->model->getContentItems($ctype_name);
        }

        if ($mode == 'unbind') {

            $unbind_perm = cmsUser::getPermissionValue($child_ctype_name, 'bind_off_parent');

            if ($unbind_perm == 'own' || $authors == 'own') {
                $this->model->filterEqual('user_id', $this->cms_user->id);
            }

            if ($item_id) {

                $join_condition = "r.parent_ctype_id = '{$ctype['id']}' AND " .
                        "r.parent_item_id = '{$item_id}' AND " .
                        'r.child_ctype_id ' . ($child_ctype['id'] ? '=' . $child_ctype['id'] : 'IS NULL' ) . ' AND ' .
                        'r.child_item_id = i.id';

                $this->model->joinInner('content_relations_bind', 'r', $join_condition);
            }

            $total = $this->model->getContentItemsCount($child_ctype_name);
            $items = $this->model->getContentItems($child_ctype_name);
        }

        return $this->cms_template->render('item_bind_list', [
            'mode'        => $mode,
            'ctype'       => $ctype,
            'child_ctype' => $child_ctype,
            'total'       => $total,
            'items'       => $items
        ]);
    }

}
