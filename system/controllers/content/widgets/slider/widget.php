<?php

class widgetContentSlider extends cmsWidget {

    public function run() {

        $cat_id           = $this->getOption('category_id');
        $ctype_id         = $this->getOption('ctype_id');
        $dataset_id       = $this->getOption('dataset');
        $image_field      = $this->getOption('image_field');
        $big_image_field  = $this->getOption('big_image_field');
        $big_image_preset = $this->getOption('big_image_preset');
        $teaser_fields    = $this->getOption('teaser_field');
        $limit            = $this->getOption('limit', 10);
        $delay            = $this->getOption('delay', 5);
        $teaser_len       = $this->getOption('teaser_len', 100);

        $model = cmsCore::getModel('content');

        $ctype = $model->getContentType($ctype_id);
        if (!$ctype) {
            return false;
        }

        if ($cat_id) {
            $category = $model->getCategory($ctype['name'], $cat_id);
        } else {
            $category = false;
        }

        if ($dataset_id) {

            $dataset = $model->getContentDataset($dataset_id);

            if ($dataset) {
                $model->applyDatasetFilters($dataset);
            } else {
                $dataset_id = false;
            }
        }

        if ($category) {
            $model->filterCategory($ctype['name'], $category, true, !empty($ctype['options']['is_cats_multi']));
        }

        // применяем приватность
        // флаг показа только названий
        $hide_except_title = $model->applyPrivacyFilter($ctype, cmsUser::isAllowed($ctype['name'], 'view_all'));

        // Скрываем записи из скрытых родителей (приватных групп и т.п.)
        $model->enableHiddenParentsFilter();

        // выключаем формирование рейтинга в хуках
        $ctype['is_rating'] = 0;

        list($ctype, $model) = cmsEventsManager::hook('content_list_filter', [$ctype, $model]);
        list($ctype, $model) = cmsEventsManager::hook("content_{$ctype['name']}_list_filter", [$ctype, $model]);

        $items = $model->limit($limit)->getContentItems($ctype['name']);
        if (!$items) {
            return false;
        }

        list($ctype, $items) = cmsEventsManager::hook("content_before_list", [$ctype, $items]);
        list($ctype, $items) = cmsEventsManager::hook("content_{$ctype['name']}_before_list", [$ctype, $items]);

        return [
            'ctype'             => $ctype,
            'teaser_len'        => $teaser_len,
            'hide_except_title' => $hide_except_title,
            'delay'             => $delay,
            'image_field'       => $image_field,
            'big_image_field'   => $big_image_field,
            'big_image_preset'  => $big_image_preset,
            'teaser_field'      => $teaser_fields,
            'items'             => $items
        ];
    }

}
