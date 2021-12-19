<?php

class actionGroupsItemChildsView extends cmsAction {

    public $lock_explicit_call = true;

    public function run($ctype, $item, $childs, $content_controller, $fields, $child_target){

        if(!empty($childs['tabs'][$this->name]['relation_id'])){
            $relation = $childs['relations'][$childs['tabs'][$this->name]['relation_id']];
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
        $datasets = $this->getDatasets();

        // Если есть наборы, применяем фильтры текущего
        if ($datasets){

            if($dataset && empty($datasets[$dataset])){ cmsCore::error404(); }

            if (!$dataset && !empty($relation['options']['dataset_id'])){

                $relation_dataset = $this->model_content->getContentDataset($relation['options']['dataset_id']);

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
                unset($current_dataset); $datasets = false;
            }

        }

        $filter =   "r.parent_ctype_id = '{$ctype['id']}' AND ".
                    "r.parent_item_id = '{$item['id']}' AND ".
                    'r.child_ctype_id IS NULL AND '.
                    "r.child_item_id = i.id AND r.target_controller = '{$this->name}'";

        $this->model->joinInner('content_relations_bind', 'r', $filter);

        if (!empty($relation['options']['limit'])){
            $this->setOption('limit', $relation['options']['limit']);
        }

        if (!empty($relation['options']['is_hide_filter'])){
            $this->setOption('is_filter', false);
        }

        $base_ds_url = href_to_rel($ctype['name'], $item['slug'].'/view-'.$this->name);

        $html = $this->renderGroupsList(rel_to_href($base_ds_url).($dataset ? '/'.$dataset : ''));

        $item_seo = $content_controller->prepareItemSeo($item, $fields, $ctype);

        $seo_title = empty($relation['seo_title']) ? LANG_GROUPS . ' - ' . $item['title'] : string_replace_keys_values_extended($relation['seo_title'], $item_seo);
        $seo_keys  = empty($relation['seo_keys']) ? '' : string_replace_keys_values_extended($relation['seo_keys'], $item_seo);
        $seo_desc  = empty($relation['seo_desc']) ? '' : string_get_meta_description(string_replace_keys_values_extended($relation['seo_desc'], $item_seo));

        $this->cms_template->setContext($content_controller);

        return $this->cms_template->render('item_childs_view', array(
            'ctype'           => $ctype,
            'child_ctype'     => array('name' => $this->name, 'title' => LANG_GROUPS),
            'item'            => $item,
            'childs'          => $childs,
            'datasets'        => $datasets,
            'dataset'         => $dataset,
            'current_dataset' => (isset($current_dataset) ? $current_dataset : array()),
            'base_ds_url'     => $base_ds_url . '%s',
            'html'            => $html,
            'relation'        => $relation,
            'seo_title'       => $seo_title,
            'seo_keys'        => $seo_keys,
            'seo_desc'        => $seo_desc
        ));

	}

}
