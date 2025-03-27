<?php

class modelWidgets extends cmsModel {

    public function getLayoutRows($template_name) {

        $this->filterEqual('r.template', $template_name);

        $this->select('r.nested_position', 'nested_position');
        $this->select('r.parent_id', 'parent_id');
        $this->select('r.title', 'row_title');
        $this->select('r.options', 'row_options');
        $this->select('r.class', 'row_class');
        $this->select('r.tag', 'row_tag');

        $this->joinInner('layout_rows', 'r', 'r.id = i.row_id');

        $this->useCache('layout.rows');

        $this->orderByList([
            ['by' => 'r.ordering', 'to' => 'asc'],
            ['by' => 'i.ordering', 'to' => 'asc']
        ]);

        $rows = [];
        $child_positions = [];
        $ns_rows = [];

        $items = $this->get('layout_cols', function($item, $model) use(&$child_positions) {

            $item['positions']   = [$item['name']];
            $item['options']     = cmsModel::stringToArray($item['options']);
            $item['row_options'] = cmsModel::stringToArray($item['row_options']);

            if ($item['parent_id']) {
                $child_positions[$item['parent_id']]   = $child_positions[$item['parent_id']] ?? [];
                $child_positions[$item['parent_id']][] = $item['name'];
            }

            return $item;
        }, false) ?: [];

        foreach ($items as $item) {

            $positions         = $child_positions[$item['id']] ?? [];
            $positions[]       = $item['name'];
            $item['positions'] = $positions;

            $row_id          = $item['row_id'];
            $parent_id       = $item['parent_id'];
            $nested_position = $item['nested_position'];

            $row_data = [
                'id'        => $row_id,
                'parent_id' => $parent_id,
                'title'     => $item['row_title'],
                'tag'       => $item['row_tag'],
                'class'     => $item['row_class'],
                'options'   => $item['row_options'],
                'positions' => $positions,
                'cols'      => [$item['id'] => $item]
            ];

            if ($parent_id) {
                if (!isset($ns_rows[$parent_id][$nested_position][$row_id])) {
                    $ns_rows[$parent_id][$nested_position][$row_id] = $row_data;
                } else {
                    $ns_rows[$parent_id][$nested_position][$row_id]['positions'][] = $item['name'];
                    $ns_rows[$parent_id][$nested_position][$row_id]['cols'][$item['id']] = $item;
                }
            } else {
                if (!isset($rows[$row_id])) {
                    $rows[$row_id] = $row_data;
                } else {
                    $rows[$row_id]['positions'] = array_merge($rows[$row_id]['positions'], $positions);
                    $rows[$row_id]['cols'][$item['id']] = $item;
                }
            }
        }

        foreach ($rows as &$row) {
            foreach ($row['cols'] as $col_id => &$col) {
                if (isset($ns_rows[$col_id])) {
                    $col['rows'] = $ns_rows[$col_id];
                }
            }
        }

        return $rows;
    }

    public function getPages() {

        $this->useCache('widgets.pages');

        return $this->get('widgets_pages', function($item, $model) {

            $item['groups']    = cmsModel::yamlToArray($item['groups']);
            $item['countries'] = cmsModel::yamlToArray($item['countries']);

            $item['url_mask'] = $item['url_mask'] ? explode("\n", $item['url_mask']) : [];

            $item['url_mask_not'] = $item['url_mask_not'] ? explode("\n", $item['url_mask_not']) : [];

            return $item;
        }, false) ?: [];
    }

    public function getWidgetsForPages($pages_list, $template){

        $this->useCache('widgets.bind');

        $widgets_bind = $this->
                select('w.controller', 'controller')->
                select('w.name', 'name')->
                select('wb.*')->
                select('i.id', 'id')->
                join('widgets_bind', 'wb', 'wb.id = i.bind_id')->
                join('widgets', 'w', 'w.id = wb.widget_id')->
                filterIn('page_id', $pages_list)->
                filterEqual('template', $template)->
                filterEqual('is_enabled', 1)->
                orderBy('i.position, i.ordering')->
                get('widgets_bind_pages', function($item, $model){

                    $item['options'] = cmsModel::yamlToArray($item['options']);
                    $item['groups_view'] = cmsModel::yamlToArray($item['groups_view']);
                    $item['groups_hide'] = cmsModel::yamlToArray($item['groups_hide']);
                    $item['languages'] = cmsModel::yamlToArray($item['languages']);
                    $item['template_layouts'] = cmsModel::yamlToArray($item['template_layouts']);
                    $item['device_types'] = cmsModel::yamlToArray($item['device_types']);

                    if (!empty($item['url_mask_not'])) {
                        $item['url_mask_not'] = explode("\n", $item['url_mask_not']);
                    }

                    return $item;
                }) ?: [];

        return cmsEventsManager::hook('widgets_before_list', $widgets_bind);
    }

}
