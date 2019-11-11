<?php

class modelWidgets extends cmsModel {

    public function addPage($page) {

        if (isset($page['url_mask']) && is_array($page['url_mask'])) {
            $page['url_mask'] = implode("\n", $page['url_mask']);
        }

        if (isset($page['url_mask_not']) && is_array($page['url_mask_not'])) {
            $page['url_mask_not'] = implode("\n", $page['url_mask_not']);
        }

        cmsCache::getInstance()->clean('widgets.pages');

        return $this->insert('widgets_pages', $page);

    }

    public function updatePage($id, $page) {

        cmsCache::getInstance()->clean('widgets.pages');

        return $this->update('widgets_pages', $id, $page);

    }

    public function deletePage($id) {

        // получаем привязанные виджеты
        $wb = $this->selectOnly('bind_id')->
                filterEqual('page_id', $id)->
                get('widgets_bind_pages', false, 'bind_id');

        // если есть, удаляем привязки и удаляем общие настройки
        if ($wb) {

            // удаляем привязки
            $this->filterEqual('page_id', $id);
            $this->deleteFiltered('widgets_bind_pages');

            // настройки виджетов
            $this->filterIn('id', array_keys($wb));
            $this->deleteFiltered('widgets_bind');

            cmsCache::getInstance()->clean('widgets.bind_pages');

        }

        cmsCache::getInstance()->clean('widgets.pages');

        return $this->delete('widgets_pages', $id);

    }

    public function getPage($id) {

        $this->useCache('widgets.pages');

        $this->joinLeft('content_types', 'ct', "i.name LIKE concat(ct.name, '.%')")->select('ct.title', 'title_subject');

        return $this->getItemById('widgets_pages', $id, function($item, $model) {

            $item['groups']    = cmsModel::yamlToArray($item['groups']);
            $item['countries'] = cmsModel::yamlToArray($item['countries']);

            $item['is_custom'] = !empty($item['title']);

            $item['title'] = !empty($item['title']) ?
                    $item['title'] :
                    sprintf(constant($item['title_const']), $item['title_subject']);

            return $item;

        });

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

        }, false);

    }

    public function getPagesByName($controller_name, $name) {

        $name = str_replace('*', '%', $name);

        $this->filterEqual('controller', $controller_name);
        $this->filterLike('name', $name);

        $this->useCache('widgets.pages');

        return $this->get('widgets_pages');

    }

    public function deletePagesByName($controller_name, $name) {

        $name = str_replace('*', '%', $name);

        $pages = $this->getPagesByName($controller_name, $name);

        if ($pages) {
            foreach ($pages as $page) {
                $this->deleteWidgetPageBind($page['id'], 'page_id');
            }
        }

        $this->filterLike('name', $name);

        cmsCache::getInstance()->clean('widgets.pages');

        return $this->deleteFiltered('widgets_pages');

    }

    public function deletePageWidgets($page_id) {

        return $this->deleteWidgetPageBind($page_id, 'page_id');

    }

    public function getPagesControllers() {

        $this->filterNotNull('controller');
        $this->joinLeft('controllers', 'ct', 'ct.name = i.controller')->filterEqual('ct.is_enabled', 1);
        $this->groupBy('controller');

        $controllers = $this->get('widgets_pages', function($item, $model) {
            return constant('LANG_' . mb_strtoupper($item['controller']) . '_CONTROLLER');
        }, 'controller');

        return array('custom' => LANG_WP_CUSTOM) + $controllers;

    }

    public function getControllerPages($controller_name) {

        if ($controller_name != 'custom') {

            $this->filterEqual('controller', $controller_name);

            if ($controller_name === 'content') {
                $this->joinLeft('content_types', 'ct', "i.name LIKE concat(ct.name, '.%')")
                        ->select('ct.title', 'title_subject')->filterEqual('ct.is_enabled', 1);
            }

        } else {
            $this->filterIsNull('controller');
        }

        $this->orderBy('name');

        return $this->get('widgets_pages', function($item, $model) {

            if (!$item['id']) {
                return false;
            }
            if (!$item['controller']) {
                $item['controller'] = 'custom';
            }

            $item['title'] = !empty($item['title']) ?
                    $item['title'] :
                    sprintf(constant($item['title_const']), $item['title_subject']);

            return $item;

        });

    }

    public function getAvailableWidgets() {

        $widgets = $this->orderByList(array(
                    array('by' => 'controller', 'to' => 'asc'),
                    array('by' => 'name', 'to' => 'asc')
                ))->get('widgets', function ($item, $model){
                    if($item['image_hint_path']){
                        $item['image_hint_path'] = cmsConfig::get('upload_host').'/'.$item['image_hint_path'];
                    }
                    return $item;
                });

        if (!$widgets) { return false; }

        $sorted = array();

        foreach ($widgets as $widget) {

            $key = $widget['controller'] ? $widget['controller'] : '0';

            $sorted[$key][] = $widget;

        }

        return $sorted;

    }

    public function getWidget($id) {

        return $this->getItemById('widgets', $id);

    }

    public function getWidgetBinding($id) {

        $this->select('w.controller', 'controller');
        $this->select('w.name', 'name');
        $this->select('w.title', 'widget_title');
        $this->select('w.image_hint_path', 'image_hint_path');

        $this->join('widgets', 'w', 'w.id = i.widget_id');

        $this->useCache('widgets.bind');

        return $this->getItemById('widgets_bind', $id, function($item, $model) {
            if($item['image_hint_path']){
                $item['image_hint_path'] = cmsConfig::get('upload_host').'/'.$item['image_hint_path'];
            }
            $item['options']          = cmsModel::yamlToArray($item['options']);
            $item['groups_view']      = cmsModel::yamlToArray($item['groups_view']);
            $item['groups_hide']      = cmsModel::yamlToArray($item['groups_hide']);
            $item['device_types']     = cmsModel::yamlToArray($item['device_types']);
            $item['template_layouts'] = cmsModel::yamlToArray($item['template_layouts']);
            $item['languages']        = cmsModel::yamlToArray($item['languages']);
            return $item;
        });

    }

    public function getWidgetBindingsScheme($page_id, $template_name) {

        $binds = $this->
            filterStart()->
            filterEqual('bp.template', $template_name)->
            filterOr()->
            filterIsNull('bp.template')->
            filterEnd()->
            filterStart()->
            filterEqual('bp.page_id', 0)->
            filterOr()->
            filterIsNull('bp.page_id')->
            filterOr()->
            filterEqual('bp.page_id', $page_id)->
            filterOr()->
            filterIsNull('bp.position')->
            filterEnd()->
            select('w.title', 'name')->
            select('w.image_hint_path', 'image_hint_path')->
            select('bp.*')->
            select('IFNULL(bp.bind_id,i.id)', 'bind_id')->
            joinInner('widgets', 'w', 'w.id = i.widget_id')->
            joinLeft('widgets_bind_pages', 'bp', 'bp.bind_id = i.id')->
            orderBy('bp.page_id, bp.ordering')->
            get('widgets_bind') ?: array();

        $positions = array();

        foreach ($binds as $bind) {

            $bind['languages']    = cmsModel::yamlToArray($bind['languages']);
            $bind['device_types'] = cmsModel::yamlToArray($bind['device_types']);

            if ($bind['device_types'] && count($bind['device_types']) < 3) {

                $device_types = [];

                foreach ($bind['device_types'] as $dt) {
                    $device_types[] = string_lang('LANG_' . $dt . '_DEVICES');
                }

            } else {
                $device_types = false;
            }

            if (!$bind['position']) {
                $bind['position'] = '_unused';
            }

            if($bind['image_hint_path']){
                $bind['image_hint_path'] = cmsConfig::get('upload_host').'/'.$bind['image_hint_path'];
            }

            $positions[$bind['position']][] = array(
                'id'           => $bind['id'],
                'bind_id'      => $bind['bind_id'],
                'widget_id'    => $bind['widget_id'],
                'title'        => $bind['title'],
                'position'     => $bind['position'],
                'languages'    => $bind['languages'],
                'image_hint_path' => $bind['image_hint_path'],
                'device_type_names' => $bind['device_types'],
                'device_types' => $device_types,
                'name'         => $bind['name'],
                'is_tab_prev'  => (bool) $bind['is_tab_prev'],
                'is_enabled'   => (bool) $bind['is_enabled'],
                'is_disabled'  => ($bind['page_id'] != $page_id && $bind['position'] !== '_unused')
            );

        }

        return $positions ? $positions : false;

    }

    public function addWidgetBinding($widget, $page_id, $position, $template) {

        cmsCache::getInstance()->clean('widgets.bind');

        $bind_id = $this->insert('widgets_bind', array(
            'widget_id' => $widget['id'],
            'title'     => $widget['title']
        ));

        if (!$bind_id) { return false; }

        $bp_id = $this->addWidgetBindPage($bind_id, $page_id, $position, $template);

        return array('id' => $bind_id, 'bp_id' => $bp_id);

    }

    public function updateWidgetBinding($id, $widget) {

        if ($widget['template_layouts'] === array('0')) {
            $widget['template_layouts'] = null;
        }
        if ($widget['languages'] === array('0')) {
            $widget['languages'] = null;
        }
        if ($widget['groups_view'] === array('0')) {
            $widget['groups_view'] = null;
        }
        if ($widget['device_types'] === array('0')) {
            $widget['device_types'] = null;
        }

        cmsCache::getInstance()->clean('widgets.bind');

        return $this->update('widgets_bind', $id, $widget);

    }

    public function deleteWidgetBinding($id) {

        $this->deleteWidgetPageBind($id, 'bind_id');

        cmsCache::getInstance()->clean('widgets.bind');

        return $this->delete('widgets_bind', $id);

    }

    public function getWidgetBindPage($id) {
        return $this->getItemById('widgets_bind_pages', $id);
    }

    public function copyWidgetByPage($bp_id) {

        $binding_page = $this->getWidgetBindPage($bp_id);
        if (!$binding_page) { return false; }

        $widget_bind = $this->getItemById('widgets_bind', $binding_page['bind_id']);
        if (!$widget_bind) { return false; }

        cmsCache::getInstance()->clean('widgets.bind');

        $ordering = $this->filterEqual('page_id', $binding_page['page_id'])->
                filterEqual('position', $binding_page['position'])->
                getNextOrdering('widgets_bind_pages');

        $widget_bind['title'] .= ' (' . $ordering . ')';
        unset($widget_bind['id']);

        $bind_id = $this->insert('widgets_bind', $widget_bind);
        if (!$bind_id) { return false; }

        $new_bp_id = $this->addWidgetBindPage($bind_id, $binding_page['page_id'], $binding_page['position'], $binding_page['template'], $ordering, $binding_page['is_enabled']);

        return array('id' => $bind_id, 'bp_id' => $new_bp_id);

    }

    public function getWidgetBindPageCount($bind_id) {
        return $this->filterEqual('bind_id', $bind_id)->getCount('widgets_bind_pages', 'id', true);
    }

    public function addWidgetBindPage($bind_id, $page_id, $position, $template, $ordering = null, $is_enabled = 1) {

        cmsCache::getInstance()->clean('widgets.bind_pages');

        return $this->insert('widgets_bind_pages', array(
            'template'   => $template,
            'page_id'    => $page_id,
            'bind_id'    => $bind_id,
            'position'   => $position,
            'is_enabled' => $is_enabled,
            'ordering'   => is_null($ordering) ? $this->
                    filterEqual('page_id', $page_id)->
                    filterEqual('position', $position)->
                    getNextOrdering('widgets_bind_pages') : $ordering
        ));

    }

    public function updateWidgetBindPage($id, $item) {
        cmsCache::getInstance()->clean('widgets.bind_pages');
        return $this->update('widgets_bind_pages', $id, $item);
    }

    public function deleteWidgetPageBind($id, $by = 'id') {
        cmsCache::getInstance()->clean('widgets.bind_pages');
        return $this->filterEqual($by, $id)->deleteFiltered('widgets_bind_pages');
    }

    public function deleteWidget($id) {

        $wb = $this->filterEqual('widget_id', $id)->get('widgets_bind');

        if ($wb) {

            $this->filterIn('bind_id', array_keys($wb));
            $this->deleteFiltered('widgets_bind_pages');

            cmsCache::getInstance()->clean('widgets.bind_pages');

        }

        $this->filterEqual('widget_id', $id);
        $this->deleteFiltered('widgets_bind');

        cmsCache::getInstance()->clean('widgets.bind');

        return $this->delete('widgets', $id);

    }

    public function reorderWidgetsBindings($position, $items, $template, $page_id = 0) {

        if ($position === '_unused') {
            $page_id = null;
        }

        $in = array();
        foreach ($items as $item) {
            if (!empty($item['bp_id'])) {
                $in[] = $item['bp_id'];
            }
        }

        $now = $in ? $this->filterIn('id', $in)->get('widgets_bind_pages') : array();
        $i   = 0;

        $new = array();

        foreach ($items as $item) {

            if (!empty($item['bp_id']) && !empty($now[$item['bp_id']])) {

                $this->updateWidgetBindPage($item['bp_id'], array(
                    'page_id'  => $page_id,
                    'ordering' => $i,
                    'position' => $position,
                    'template' => $template
                ));

            } else { // копия

                if ($this->filterEqual('id', $item['id'])->getCount('widgets_bind', 'id', true)) {

                    $id = $this->addWidgetBindPage($item['id'], $page_id, $position, $template, $i, ($position === '_unused') ? 0 : 1);

                    $new[$id] = $item['id']; // $item['id'] может быть одинаковый

                }

            }

            $i++;

        }

        return $new;

    }

    public function unbindAllWidgets($template_name) {
        $this->deleteWidgetPageBind($template_name, 'template');
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
                filterEqual('template', $template)->
                filterEqual('is_enabled', 1)->
                filterIn('page_id', $pages_list)->
                orderBy('i.page_id, i.position, i.ordering')->forceIndex('page_id')->
                get('widgets_bind_pages', function($item, $model){

                    $item['options'] = cmsModel::yamlToArray($item['options']);
                    $item['groups_view'] = cmsModel::yamlToArray($item['groups_view']);
                    $item['groups_hide'] = cmsModel::yamlToArray($item['groups_hide']);
                    $item['languages'] = cmsModel::yamlToArray($item['languages']);
                    $item['template_layouts'] = cmsModel::yamlToArray($item['template_layouts']);
                    $item['device_types'] = cmsModel::yamlToArray($item['device_types']);

                    return $item;

                });

        if(!$widgets_bind){ return cmsEventsManager::hook('widgets_before_list', array()); }

        return cmsEventsManager::hook('widgets_before_list', $widgets_bind);

    }

}
