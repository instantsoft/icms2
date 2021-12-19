<?php

class actionContentItemChildsView extends cmsAction {

    public $lock_explicit_call = true;

    public function run($ctype, $item, $child_ctype_name, $childs, $fields){

        $child_ctype = $this->model->getContentTypeByName($child_ctype_name);

        if (!$child_ctype) {
            cmsCore::error404();
        }

        if(!empty($childs['tabs'][$child_ctype_name]['relation_id'])){
            $relation = $childs['relations'][$childs['tabs'][$child_ctype_name]['relation_id']];
        } else {
            cmsCore::error404();
        }

        if (!in_array($relation['layout'], array('tab', 'hidden'))) {
            cmsCore::error404();
        }

        // Текущий набор
        $dataset = $this->request->get('dataset', '');

        // устанавливаем контекст списка
        $this->setListContext('item_view_relation_tab');

        // Получаем список наборов
        $datasets = $this->getCtypeDatasets($child_ctype, array(
            'cat_id' => 0
        ));

        // Если есть наборы, применяем фильтры текущего
        $current_dataset = array();
        if ($datasets){

            if($dataset && empty($datasets[$dataset])){ cmsCore::error404(); }

            if (!$dataset && !empty($relation['options']['dataset_id'])){

                $relation_dataset = $this->model->getContentDataset($relation['options']['dataset_id']);

                if ($relation_dataset){
                    $dataset = $relation_dataset['name'];
                }

            }

            $keys = array_keys($datasets);
            $current_dataset = $dataset ? $datasets[$dataset] : $datasets[$keys[0]];
            $this->model->applyDatasetFilters($current_dataset);
            // устанавливаем максимальное количество записей для набора, если задано
            if(!empty($current_dataset['max_count'])){
                $this->max_items_count = $current_dataset['max_count'];
            }
            // если набор всего один, например для изменения сортировки по умолчанию,
            // не показываем его на сайте
            if(count($datasets) == 1){
                $current_dataset = array(); $datasets = false;
            }

        }

        $filter =   "r.parent_ctype_id = {$ctype['id']} AND ".
                    "r.parent_item_id = {$item['id']} AND ".
                    "r.child_ctype_id = {$child_ctype['id']} AND ".
                    "r.child_item_id = i.id";

        $this->model->joinInner('content_relations_bind', 'r', $filter);

        if (!empty($relation['options']['limit'])){
            $child_ctype['options']['limit'] = $relation['options']['limit'];
        }

        if (!empty($relation['options']['is_hide_filter'])){
            $child_ctype['options']['list_show_filter'] = false;
        }

        $base_ds_url = href_to_rel($ctype['name'], $item['slug'].'/view-'.$child_ctype_name);

        // кешируем
        cmsModel::cacheResult('current_ctype', $ctype);
        cmsModel::cacheResult('current_ctype_dataset', $current_dataset);
        cmsModel::cacheResult('current_ctype_item', $item);
        cmsModel::cacheResult('current_child_ctype', $child_ctype);

        $html = $this->renderItemsList($child_ctype, rel_to_href($base_ds_url).($dataset ? '/'.$dataset : ''));

        $item_seo = $this->prepareItemSeo($item, $fields, $ctype);

        $seo_title = empty($relation['seo_title']) ? $child_ctype['title'] . ' - ' . $item['title'] : string_replace_keys_values_extended($relation['seo_title'], $item_seo);
        $seo_keys  = empty($relation['seo_keys']) ? '' : string_replace_keys_values_extended($relation['seo_keys'], $item_seo);
        $seo_desc  = empty($relation['seo_desc']) ? '' : string_get_meta_description(string_replace_keys_values_extended($relation['seo_desc'], $item_seo));

        list($ctype, $item, $child_ctype, $childs) = cmsEventsManager::hook('content_childs_view', array($ctype, $item, $child_ctype, $childs));
        list($ctype, $item, $child_ctype, $childs) = cmsEventsManager::hook("content_{$ctype['name']}_childs_view", array($ctype, $item, $child_ctype, $childs));

        $toolbar_html = cmsEventsManager::hookAll('content_toolbar_html', array($child_ctype['name'], array(), $current_dataset, array(
            array(
                'field'     => 'relation',
                'condition' => 'inner',
                'value'     => array(
                    'parent_ctype_id' => $ctype['id'],
                    'parent_item_id'  => $item['id'],
                    'child_ctype_id'  => $child_ctype['id']
                )
            )
        )));

        return $this->cms_template->render('item_childs_view', array(
            'toolbar_html'    => $toolbar_html,
            'ctype'           => $ctype,
            'child_ctype'     => $child_ctype,
            'item'            => $item,
            'childs'          => $childs,
            'html'            => $html,
            'relation'        => $relation,
            'datasets'        => $datasets,
            'dataset'         => $dataset,
            'current_dataset' => $current_dataset,
            'base_ds_url'     => $base_ds_url . '%s',
            'seo_title'       => $seo_title,
            'seo_keys'        => $seo_keys,
            'seo_desc'        => $seo_desc
        ));

	}

}
