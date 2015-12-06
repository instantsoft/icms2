<?php

class widgetContentSlider extends cmsWidget {

    public function run(){

        $cat_id           = $this->getOption('category_id');
        $ctype_id         = $this->getOption('ctype_id');
        $dataset_id       = $this->getOption('dataset');
        $image_field      = $this->getOption('image_field');
        $big_image_field  = $this->getOption('big_image_field');
        $big_image_preset = $this->getOption('big_image_preset');
        $teaser_fields    = $this->getOption('teaser_field');
        $limit            = $this->getOption('limit', 10);
        $delay            = $this->getOption('delay', 5);

        $model = cmsCore::getModel('content');

        $ctype = $model->getContentType($ctype_id);
        if (!$ctype) { return false; }

		if ($cat_id){
			$category = $model->getCategory($ctype['name'], $cat_id);
		} else {
			$category = false;
		}

        if ($dataset_id){

            $dataset = $model->getContentDataset($dataset_id);

            if ($dataset){
                $model->applyDatasetFilters($dataset);
            } else {
                $dataset_id = false;
            }

        }

		if ($category){
			$model->filterCategory($ctype['name'], $category, true);
		}

        // Отключаем фильтр приватности для тех кому это разрешено
        if (!empty($ctype['options']['privacy_type']) && in_array($ctype['options']['privacy_type'], array('show_title', 'show_all'), true)) {
            $model->disablePrivacyFilter();
        }
        if (cmsUser::isAllowed($ctype['name'], 'view_all')) {
            $model->disablePrivacyFilter();
        }

        // Скрываем записи из скрытых родителей (приватных групп и т.п.)
        $model->filterHiddenParents();

		list($ctype, $model) = cmsEventsManager::hook("content_list_filter", array($ctype, $model));
		list($ctype, $model) = cmsEventsManager::hook("content_{$ctype['name']}_list_filter", array($ctype, $model));

        $items = $model->limit($limit)->getContentItems($ctype['name']);
        if (!$items) { return false; }

        return array(
            'ctype'            => $ctype,
            'delay'            => $delay,
            'image_field'      => $image_field,
            'big_image_field'  => $big_image_field,
            'big_image_preset' => $big_image_preset,
            'teaser_field'     => $teaser_fields,
            'items'            => $items
        );

    }

}