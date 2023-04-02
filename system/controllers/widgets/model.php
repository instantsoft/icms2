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

        $items = $this->get('layout_cols', function($item, $model) {
            $item['positions'] = [$item['name']];
            $item['options'] = cmsModel::stringToArray($item['options']);
            $item['row_options'] = cmsModel::stringToArray($item['row_options']);
            return $item;
        }, false);

        $rows = $ns_rows = $child_positions = [];

        if($items){

            $getRowItem = function($item, $positions){
                $item['positions'] = $positions;
                return [
                    'id'        => $item['row_id'],
                    'parent_id' => $item['parent_id'],
                    'title'     => $item['row_title'],
                    'tag'       => $item['row_tag'],
                    'class'     => $item['row_class'],
                    'options'   => $item['row_options'],
                    'positions' => $positions,
                    'cols'      => [$item['id'] => $item]
                ];
            };

            foreach ($items as $item) {
                if($item['parent_id']){

                    if(!isset($child_positions[$item['parent_id']])){
                        $child_positions[$item['parent_id']] = [$item['name']];
                    } else {
                        $child_positions[$item['parent_id']][] = $item['name'];
                    }

                    if(!isset($ns_rows[$item['parent_id']][$item['nested_position']][$item['row_id']])){
                        $ns_rows[$item['parent_id']][$item['nested_position']][$item['row_id']] = $getRowItem($item, [$item['name']]);
                    } else {
                        $ns_rows[$item['parent_id']][$item['nested_position']][$item['row_id']]['positions'][] = $item['name'];
                        $ns_rows[$item['parent_id']][$item['nested_position']][$item['row_id']]['cols'][$item['id']] = $item;
                    }
                }
            }

            foreach ($items as $item) {

                if($item['parent_id']){
                    continue;
                }
                if(isset($ns_rows[$item['id']])){
                    $item['rows'] = $ns_rows[$item['id']];
                }

                if(!isset($rows[$item['row_id']])){

                    $positions = [];
                    if(isset($child_positions[$item['id']])){
                        $positions = $child_positions[$item['id']];
                    }
                    $positions[] = $item['name'];

                    $rows[$item['row_id']] = $getRowItem($item, $positions);
                } else {
                    $rows[$item['row_id']]['positions'][] = $item['name'];
                    $rows[$item['row_id']]['cols'][$item['id']] = $item;
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

            if ($item['url_mask']) {
                $item['url_mask'] = explode("\n", $item['url_mask']);
            }
            if ($item['url_mask_not']) {
                $item['url_mask_not'] = explode("\n", $item['url_mask_not']);
            }

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
                orderBy('i.position, i.ordering')->forceIndex('page_id')->
                get('widgets_bind_pages', function($item, $model){

                    $item['options'] = cmsModel::yamlToArray($item['options']);
                    $item['groups_view'] = cmsModel::yamlToArray($item['groups_view']);
                    $item['groups_hide'] = cmsModel::yamlToArray($item['groups_hide']);
                    $item['languages'] = cmsModel::yamlToArray($item['languages']);
                    $item['template_layouts'] = cmsModel::yamlToArray($item['template_layouts']);
                    $item['device_types'] = cmsModel::yamlToArray($item['device_types']);

                    return $item;
                }) ?: [];

        return cmsEventsManager::hook('widgets_before_list', $widgets_bind);
    }

}
