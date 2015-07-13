<?php
class modelWidgets extends cmsModel {

    public function addPage($page){

        if (isset($page['url_mask']) && is_array($page['url_mask'])){
            $page['url_mask'] = implode("\n", $page['url_mask']);
        }

        if (isset($page['url_mask_not']) && is_array($page['url_mask_not'])){
            $page['url_mask_not'] = implode("\n", $page['url_mask_not']);
        }

        cmsCache::getInstance()->clean("widgets.pages");

        return $this->insert('widgets_pages', $page);

    }

    public function updatePage($id, $page){

        cmsCache::getInstance()->clean("widgets.pages");

        return $this->update('widgets_pages', $id, $page);

    }

    public function deletePage($id){

        $this->filterEqual('page_id', $id);
        $this->deleteFiltered('widgets_bind');

        cmsCache::getInstance()->clean("widgets.pages");

        return $this->delete('widgets_pages', $id);

    }

    public function getPage($id){

        $this->useCache("widgets.pages");

        return $this->getItemById('widgets_pages', $id, function($item, $model){

            $item['is_custom'] = !empty($item['title']);

            $item['title'] = !empty($item['title']) ?
                                $item['title'] :
                                sprintf( constant($item['title_const']), $item['title_subject'] );

            return $item;

        });

    }

    public function getPages(){

        $this->useCache('widgets.pages');

        return $this->get('widgets_pages', function($item, $model){

            if ($item['url_mask']) { $item['url_mask'] = explode("\n", $item['url_mask']); }
            if ($item['url_mask_not']) { $item['url_mask_not'] = explode("\n", $item['url_mask_not']); }

            return $item;

        });

    }

    public function getPagesByName($controller_name, $name){

        $name = str_replace('*', '%', $name);

        $this->filterEqual('controller', $controller_name);
        $this->filterLike('name', $name);

        $this->useCache('widgets.pages');

        return $this->get('widgets_pages');

    }

    public function deletePagesByName($controller_name, $name){

        $name = str_replace('*', '%', $name);

        $pages = $this->getPagesByName($controller_name, $name);

        if ($pages){
            foreach($pages as $page){
                $this->deletePageWidgets($page['id']);
            }
        }

        $this->filterLike('name', $name);

        cmsCache::getInstance()->clean("widgets.pages");

        return $this->deleteFiltered('widgets_pages');

    }

    public function deletePageWidgets($page_id){

        $this->filterEqual('page_id', $page_id);

        cmsCache::getInstance()->clean("widgets.bind");

        return $this->deleteFiltered('widgets_bind');

    }

    public function getPagesControllers(){

        $this->filterNotNull('controller');
        $this->groupBy('controller');

        $controllers = $this->get('widgets_pages', function($item, $model){
            return constant('LANG_'.mb_strtoupper($item['controller']).'_CONTROLLER');
        }, 'controller');

        $controllers = array('custom' => LANG_WP_CUSTOM) + $controllers;

        return $controllers;

    }

    public function getControllerPages($controller_name){

        if ($controller_name != 'custom'){
            $this->filterNotNull('controller');
            $this->filterEqual('controller', $controller_name);
        } else {
            $this->filterIsNull('controller');
        }

        return $this->get('widgets_pages', function($item, $model){

            if (!$item['controller']) { $item['controller'] = 'custom'; }

            $item['title'] = !empty($item['title']) ?
                                $item['title'] :
                                sprintf( constant($item['title_const']), $item['title_subject'] );

            return $item;

        });

    }

    public function getAvailableWidgets(){

        $widgets = $this->orderBy('name')->get('widgets');

        if (!$widgets){ return false; }

        $sorted = array();

        foreach($widgets as $widget){

            $key = $widget['controller'] ? $widget['controller'] : '0';

            $sorted[ $key ][] = $widget;

        }

        return $sorted;

    }

    public function getWidget($id){

        return $this->getItemById('widgets', $id);

    }

    public function getWidgetBinding($id){

        $this->select('w.controller', 'controller');
        $this->select('w.name', 'name');

        $this->join('widgets', 'w', 'w.id = i.widget_id');

        $this->useCache("widgets.bind");

        return $this->getItemById('widgets_bind', $id, function($item, $model){
            $item['options'] = cmsModel::yamlToArray($item['options']);
            $item['groups_view'] = cmsModel::yamlToArray($item['groups_view']);
            $item['groups_hide'] = cmsModel::yamlToArray($item['groups_hide']);
            return $item;
        });

    }

    public function getWidgetBindingsScheme($page_id){

        $binds = $this->
                    filterStart()->
                        filterEqual('page_id', 0)->
                    filterEnd()->
                    filterOr()->
                    filterStart()->
                        filterEqual('page_id', $page_id)->
                    filterEnd()->
                    filterOr()->
                    filterStart()->
                        filterEqual('position', '_unused')->
                    filterEnd()->
                    orderBy('page_id, ordering')->
                    get('widgets_bind');

        $positions = array();

        foreach($binds as $bind){

            $positions[ $bind['position'] ][] = array(
                'id' => $bind['id'],
                'title' => $bind['title'],
                'is_tab_prev' => (bool)$bind['is_tab_prev'],
                'is_disabled' => $bind['page_id'] != $page_id && $bind['position'] != '_unused'
            );

        }

        return $positions ? $positions : false;

    }

    public function addWidgetBinding($widget, $page_id, $position){

        cmsCache::getInstance()->clean("widgets.bind");

        return $this->insert('widgets_bind', array(
            'widget_id' => $widget['id'],
            'title' => $widget['title'],
            'page_id' => $page_id,
            'position' => $position,
            'ordering' => $this->
                                filterEqual('page_id', $page_id)->
                                filterEqual('position', $position)->
                                getMaxOrdering('widgets_bind')
        ));

    }

    public function updateWidgetBinding($id, $widget){

        cmsCache::getInstance()->clean("widgets.bind");

        return $this->update('widgets_bind', $id, $widget);

    }

    public function deleteWidgetBinding($id){

        cmsCache::getInstance()->clean("widgets.bind");

        return $this->delete('widgets_bind', $id);

    }

    public function reorderWidgetsBindings($position, $items, $page_id=0){

        cmsCache::getInstance()->clean("widgets.bind");

        $this->reorderByList('widgets_bind', $items, array('position'=>$position));

        $update_data = array('page_id'=>$page_id);

        if ($position == '_unused'){ $update_data['page_id'] = null; }

        $this->
            filterIn('id', $items)->
            updateFiltered('widgets_bind', $update_data);

    }

    public function getWidgetsForPages($pages_list){

        $this->useCache('widgets.bind');

        return $this->
                    select('w.controller', 'controller')->
                    select('w.name', 'name')->
                    join('widgets', 'w', 'w.id = i.widget_id')->
                    filterIn('page_id', $pages_list)->
                    orderBy('page_id, position, ordering')->
                    get('widgets_bind', function($item, $model){
                        $item['options'] = cmsModel::yamlToArray($item['options']);
                        $item['groups_view'] = cmsModel::yamlToArray($item['groups_view']);
                        $item['groups_hide'] = cmsModel::yamlToArray($item['groups_hide']);
                        return $item;
                    });

    }

}
