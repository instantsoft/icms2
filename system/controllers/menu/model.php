<?php

class modelMenu extends cmsModel {

    private static $all_menus      = null;
    private static $rendered_menus = [];

    private static function loadAllMenus() {
        if (self::$all_menus === null) {

            $model = new self();

            self::$all_menus = $model->filterEqual('is_enabled', 1)->getAllMenuItemsTree();
        }
    }

    public static function getMenuItemsByName($menu_name) {

        self::loadAllMenus();

        if (!empty(self::$all_menus[$menu_name])) {

            if (!in_array($menu_name, self::$rendered_menus)) {

                self::$rendered_menus[] = $menu_name;

                self::$all_menus[$menu_name] = self::buildMenu(self::$all_menus[$menu_name]);
            }

            return self::$all_menus[$menu_name];
        }

        return [];
    }

    public function addMenu($item) {

        cmsCache::getInstance()->clean('menu.items');

        return $this->insert('menu', $item);
    }

    public function updateMenu($id, $item) {

        cmsCache::getInstance()->clean('menu.items');

        return $this->update('menu', $id, $item);
    }

    public function deleteMenu($id) {

        cmsCache::getInstance()->clean('menu.items');

        $this->delete('menu', $id);

        return $this->filterEqual('menu_id', $id)->deleteFiltered('menu_items');
    }

    public function getMenus() {

        return $this->limit(false)->orderBy('id', 'asc')->get('menu') ?: [];
    }

    public function getMenu($id, $by_field = 'id') {

        $this->useCache('menu.menus');

        return $this->getItemByField('menu', $by_field, $id);
    }

    public function getMenuItems($menu_id = false, $parent_id = false) {

        $this->useCache('menu.items');

        $this->select('COUNT(childs.id)', 'childs_count');

        $this->joinLeft('menu_items', 'childs', 'childs.parent_id = i.id AND childs.is_enabled = 1');

        if ($menu_id !== false) {
            $this->filterEqual('menu_id', $menu_id);
        }

        if ($parent_id !== false) {
            $this->filterEqual('parent_id', $parent_id);
        }

        $this->groupBy('id')->orderBy('ordering', 'asc');

        return $this->get('menu_items', function ($item, $model) {
            $item['options'] = cmsModel::yamlToArray($item['options']);
            $item['groups_view'] = cmsModel::yamlToArray($item['groups_view']);
            $item['groups_hide'] = cmsModel::yamlToArray($item['groups_hide']);
            return $item;
        });
    }

    public function getAllMenuItemsTree() {

        $menus = $this->select('menu.name', 'menu_name')->joinLeft('menu', 'menu', 'menu.id = i.menu_id')->getMenuItems();

        $result = [];

        if ($menus) {
            $menus = cmsEventsManager::hook('menu_before_list', $menus);
            foreach ($menus as $menu) {
                $result[$menu['menu_name']][$menu['id']] = $menu;
            }
        }

        return $result;
    }

    public static function buildMenu($menus, $parse_hooks = true) {

        $replaced = [
            'csrf_token' => cmsForm::getCSRFToken()
        ];

        $items = [];
        $delta = [];

        $user = cmsUser::getInstance();

        // перебираем все вернувшиеся пункты меню
        foreach ($menus as $item) {

            $is_root_added = false;

            if (($item['groups_view'] && !$user->isInGroups($item['groups_view'])) ||
                    ($item['groups_hide'] && $user->isInGroups($item['groups_hide']))) {

                if ($item['parent_id']) {
                    if (!isset($delta[$item['parent_id']])) {
                        $delta[$item['parent_id']] = 1;
                    } else {
                        $delta[$item['parent_id']] += 1;
                    }
                }

                continue;
            }

            $hook_result = ['items' => []];

            if ($item['title'] && $parse_hooks) {
                if (strpos($item['title'], '{user.') !== false) {
                    $item['title'] = string_replace_user_properties($item['title']);
                }
            }

            if ($item['url'] && $parse_hooks) {

                $matches = [];

                // Общие замены
                $item['url'] = string_replace_keys_values($item['url'], $replaced);

                // если URL пункта меню содержит свойство пользователя
                if (strpos($item['url'], '{user.') !== false) {
                    $item['url'] = string_replace_user_properties($item['url']);
                } else

                // если URL пункта меню содержит шаблон {controller:action}
                if (preg_match('/^{([a-z0-9]+):*([a-z0-9_]*)}$/i', $item['url'], $matches)) {

                    // то вызываем хук menu указанного контроллера
                    $controller = $matches[1];
                    $action     = $matches[2];

                    $hook_result = cmsEventsManager::hook('menu_' . $controller, [
                        'action'        => $action,
                        'menu_item_id'  => $item['id'],
                        'menu_item_url' => $item['url'],
                        'menu_item'     => $item
                    ], false);

                    // если хук вернул результат
                    if ($hook_result) {

                        // получаем новый URL пункта меню
                        $item['url'] = isset($hook_result['url']) ? $hook_result['url'] : '';

                        if (isset($hook_result['counter'])) {
                            $item['counter'] = $hook_result['counter'];
                        }

                        if (isset($hook_result['title'])) {
                            $item['title'] = $hook_result['title'];
                        }

                        if (!empty($hook_result['items'])) {
                            $item['childs_count'] = count($hook_result['items']);
                        }

                        $is_root_added = true;
                    } else {
                        continue;
                    }
                }

                $is_external = mb_strpos($item['url'], '://') !== false;
                $is_hash     = mb_strpos($item['url'], '#') === 0;

                if (!$is_root_added && !$is_external && !$is_hash) {
                    $item['url'] = href_to($item['url']);
                }
            }

            // добавляем обработанную строку в результирующий массив
            $items[$item['id']] = $item;

            // получаем дополнительные пункты меню
            if (!empty($hook_result['items'])) {
                foreach ($hook_result['items'] as $i) {
                    $i['menu_id']    = $item['menu_id'];
                    $i['options']    = isset($i['options']) ? $i['options'] : [];
                    $i['options']    = array_merge($item['options'], $i['options']);
                    $items[$i['id']] = $i;
                }
            }
        }

        if ($delta) {
            foreach ($delta as $item_id => $d) {
                if (isset($items[$item_id])) {
                    $items[$item_id]['childs_count'] -= $d;
                }
            }
        }

        $tree = [];

        cmsModel::buildTreeRecursive($items, $tree);

        // возвращаем дерево
        return $tree;
    }

    public function getMenuItemsTree($menu_id, $parse_hooks = true) {

        $result = $this->getMenuItems($menu_id);

        if (!$result) {
            return false;
        }

        return self::buildMenu($result, $parse_hooks);
    }

    public function getMenuItem($id) {

        return $this->getItemById('menu_items', $id, function ($item, $model) {
            $item['options'] = cmsModel::yamlToArray($item['options']);
            $item['groups_view'] = cmsModel::yamlToArray($item['groups_view']);
            $item['groups_hide'] = cmsModel::yamlToArray($item['groups_hide']);
            return $item;
        });
    }

    public function addMenuItem($item) {

        $this->filterEqual('parent_id', $item['parent_id']);

        $item['ordering'] = $this->getNextOrdering('menu_items');

        cmsCache::getInstance()->clean('menu.items');

        return $this->insert('menu_items', $item);
    }

    public function updateMenuItem($id, $item) {

        cmsCache::getInstance()->clean('menu.items');

        return $this->update('menu_items', $id, $item);
    }

    public function deleteMenuItem($id) {

        $item = $this->getMenuItem($id);

        $tree = $this->getMenuItemsTree($item['menu_id'], false);

        $level      = false;
        $node_start = false;
        $to_delete  = [$id];
        $to_reorder = [];

        foreach ($tree as $item) {

            if ($item['id'] == $id) {
                $node_start = true;
                $level      = $item['level'];
                continue;
            }

            if ($node_start) {
                if ($item['level'] > $level) {
                    $to_delete[] = $item['id'];
                    continue;
                } else {
                    $node_start = false;
                }
            }

            $to_reorder[] = $item['id'];
        }

        foreach ($to_delete as $item_id) {
            $this->delete('menu_items', $item_id);
        }

        $this->reorderByList('menu_items', $to_reorder);

        cmsCache::getInstance()->clean('menu.items');

        return true;
    }

}
