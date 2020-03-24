<?php
class widgetContentList extends cmsWidget {

    public function run(){

        $ctype_id        = $this->getOption('ctype_id');
        $dataset_id      = $this->getOption('dataset');
        $relation_id     = $this->getOption('relation_id');
        $cat_id          = $this->getOption('category_id');
        $image_field     = $this->getOption('image_field');
        $teaser_field    = $this->getOption('teaser_field');
        $is_show_details = $this->getOption('show_details');
        $style           = $this->getOption('style', 'basic');
        $limit           = $this->getOption('limit', 10);
        $teaser_len      = $this->getOption('teaser_len', 100);

        $current_ctype_item = cmsModel::getCachedResult('current_ctype_item');
        $current_ctype = cmsModel::getCachedResult('current_ctype');

        $model = cmsCore::getModel('content');

        if($ctype_id){

            $ctype = $model->getContentType($ctype_id);

        } else {

            $ctype = $current_ctype;

        }

        if (!$ctype) { return false; }

		if ($cat_id){
			$category = $model->getCategory($ctype['name'], $cat_id);
		} else {
			$category = false;
		}

        if ($dataset_id){
            $dataset = $model->getContentDataset($dataset_id);
        }

        if ($relation_id && $current_ctype_item && $current_ctype){

            $parents = $model->getContentTypeParents($ctype_id);

            if ($parents){
                foreach($parents as $parent){
                    if ($parent['id'] == $relation_id){

                        $filter =   "r.parent_ctype_id = {$current_ctype['id']} AND ".
                                    "r.parent_item_id = {$current_ctype_item['id']} AND ".
                                    "r.child_ctype_id = {$ctype_id} AND ".
                                    "r.child_item_id = i.id";

                        $this->disableCache();

                        $model->joinInner('content_relations_bind', 'r', $filter);

                        $this->title = string_replace_keys_values($this->title, $current_ctype_item);

                        $this->links = str_replace('{list_link}', href_to($current_ctype['name'], $current_ctype_item['slug'], "view-{$ctype['name']}"), $this->links);

                        break;

                    }
                }
            }

        }

        if (!empty($dataset)){
            $model->applyDatasetFilters($dataset);
        }

		if ($category){
			$model->filterCategory($ctype['name'], $category, true);
		}

        // применяем приватность
        // флаг показа только названий
        $hide_except_title = $model->applyPrivacyFilter($ctype, cmsUser::isAllowed($ctype['name'], 'view_all'));

        // Скрываем записи из скрытых родителей (приватных групп и т.п.)
        $model->enableHiddenParentsFilter();

        if($this->getOption('widget_type') == 'related'){
            if($current_ctype_item){

                $this->disableCache();

                $model->filterRelated('title', $current_ctype_item['title']);

                if($current_ctype_item['ctype_name'] == $ctype['name']){
                    $model->filterNotEqual('id', $current_ctype_item['id']);
                }


            } else {
                return false;
            }
        }

        // мы на странице группы?
        $current_group = cmsModel::getCachedResult('current_group');
        if($this->getOption('auto_group') && $current_group){

            $this->disableCache();

            $model->filterEqual('parent_id', $current_group['id'])->
                filterEqual('parent_type', 'group');

        }

        // выключаем формирование рейтинга в хуках
        $ctype['is_rating'] = 0;

		list($ctype, $model) = cmsEventsManager::hook("content_list_filter", array($ctype, $model));
		list($ctype, $model) = cmsEventsManager::hook("content_{$ctype['name']}_list_filter", array($ctype, $model));

        $items = $model->
                    limit($limit)->
                    getContentItems($ctype['name']);
        if (!$items) { return false; }

        list($ctype, $items) = cmsEventsManager::hook("content_before_list", array($ctype, $items));
        list($ctype, $items) = cmsEventsManager::hook("content_{$ctype['name']}_before_list", array($ctype, $items));

        if($style){
            $this->setTemplate('list_'.$style);
        } else {
            $this->setTemplate($this->tpl_body);
        }

        return array(
            'ctype'             => $ctype,
            'hide_except_title' => $hide_except_title,
            'teaser_len'        => $teaser_len,
            'image_field'       => $image_field,
            'teaser_field'      => $teaser_field,
            'is_show_details'   => $is_show_details,
            'style'             => $style,
            'items'             => $items
        );

    }

}
