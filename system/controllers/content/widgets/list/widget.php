<?php
class widgetContentList extends cmsWidget {

    public function run(){

        $ctype_id        = $this->getOption('ctype_id');
        $dataset_id      = $this->getOption('dataset');
        $cat_id          = $this->getOption('category_id');
        $image_field     = $this->getOption('image_field');
        $teaser_field    = $this->getOption('teaser_field');
        $is_show_details = $this->getOption('show_details');
        $style           = $this->getOption('style', 'basic');
        $limit           = $this->getOption('limit', 10);

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
			$model->groupBy('i.id');
		}

        if (!$dataset_id){
            $model->orderBy('date_pub', 'desc');
        }

        // Отключаем фильтр приватности для тех кому это разрешено
        if (cmsUser::isAllowed($ctype['name'], 'view_all')) {
            $model->disablePrivacyFilter();
        }

        // Скрываем записи из скрытых родителей (приватных групп и т.п.)
        $model->filterHiddenParents();

        $items = $model->
                    limit($limit)->
                    getContentItems($ctype['name']);
        if (!$items) { return false; }

        if($style){
            $this->setTemplate('list_'.$style);
        } else {
            $this->setTemplate($this->tpl_body);
        }

        return array(
            'ctype' => $ctype,
            'image_field' => $image_field,
            'teaser_field' => $teaser_field,
            'is_show_details' => $is_show_details,
            'style' => $style,
            'items' => $items
        );

    }

}
