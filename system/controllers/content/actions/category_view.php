<?php

class actionContentCategoryView extends cmsAction {

    public function run(){

        // Получаем название типа контента и сам тип
        $ctype_name = $this->request->get('ctype_name', '');
        $ctype = $this->model->getContentTypeByName($ctype_name);

        if (!$ctype) {
            // смотрим, не переопределено ли
            $_ctype_name = $this->getCtypeByAlias($ctype_name);
            if($_ctype_name){
                $ctype = $this->model->getContentTypeByName($_ctype_name);
            }
            if(!$ctype){
                cmsCore::error404();
            } else {
                $this->cms_core->uri_controller_before_remap = $ctype_name;
            }
        }
        if (!$ctype['options']['list_on']) { cmsCore::error404(); }

        $category = array('id' => false);
        $subcats = array();

        // Получаем SLUG категории
        $slug = $this->request->get('slug', '');

        if (!$ctype['is_cats'] && $slug != 'index') { cmsCore::error404(); }

        if ($ctype['is_cats'] && $slug != 'index') {
            $category = $this->model->getCategoryBySLUG($ctype['name'], $slug);
            if (!$category){ cmsCore::error404(); }
        }

        // Получаем список подкатегорий для текущей
        if ($ctype['is_cats']) {
            $current_cat_id = $category['id'] ? $category['id'] : 1;
            $subcats = $this->model->getSubCategories($ctype['name'], $current_cat_id);
        }

        // Получаем список наборов
        $datasets = $this->model->getContentDatasets($ctype['id'], true, function ($item, $model) use ($category) {

            $is_view = !$item['cats_view'] || in_array($category['id'], $item['cats_view']);
            $is_user_hide = $item['cats_hide'] && in_array($category['id'], $item['cats_hide']);

            if (!$is_view || $is_user_hide) { return false; }

            return $item;
        });

        list($datasets, $ctype) = cmsEventsManager::hook('content_datasets', array($datasets, $ctype));
        list($datasets, $ctype) = cmsEventsManager::hook('content_'.$ctype['name'].'_datasets', array($datasets, $ctype));

        // Текущий набор
        $dataset = $this->request->get('dataset', '');

        // Это вывод на главной?
        $is_frontpage = $this->request->get('is_frontpage', false);

        // Номер страницы
        $page = $this->request->get('page', 1);

        // Если это не главная, но данный контент выводится на главной и сейчас
        // открыта индексная страница контента - редиректим на главную
        if (!$is_frontpage && $this->cms_config->frontpage == "content:{$ctype['name']}" && $slug == 'index' && !$dataset && $page==1){
			$query = $this->cms_core->uri_query;
			if ($query){
				$this->redirect(href_to_home() . '?' . http_build_query($query));
			} else {
				$this->redirectToHome();
			}
        }

        // Если есть наборы, применяем фильтры текущего
        // иначе будем сортировать по дате создания
        if ($datasets){
            if($dataset && empty($datasets[$dataset])){ cmsCore::error404(); }
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

        // Фильтр по категории
        if ($ctype['is_cats']) {
            if($slug != 'index'){
                $this->model->filterCategory($ctype['name'], $category, $ctype['is_cats_recursive']);
            } elseif(!$ctype['is_cats_recursive']){
                $this->model->filterCategory($ctype['name'], array('id' => 1));
            }
        }

        // Скрываем записи из скрытых родителей (приватных групп и т.п.)
        $this->model->filterHiddenParents();

        // Формируем базовые URL для страниц
        $page_url = array(
            'base'  => href_to($ctype['name'] . ($dataset ? '-'.$dataset : ''), isset($category['slug']) ? $category['slug'] : ''),
            'first' => href_to($ctype['name'] . ($dataset ? '-'.$dataset : ''), isset($category['slug']) ? $category['slug'] : '')
        );

        // если не на главной
        if(!$is_frontpage){
            // если название переопределено, то редиректим со старого на новый адрес
            $mapping = cmsConfig::getControllersMapping();
            if($mapping){
                foreach($mapping as $name=>$alias){
                    if ($name == $ctype['name'] && !$this->cms_core->uri_controller_before_remap) {
                        $this->redirect(href_to($alias . ($dataset ? '-'.$dataset : ''), isset($category['slug']) ? $category['slug'] : ''), 301);
                    }
                }
            }
        }

        list($ctype, $category) = cmsEventsManager::hook("content_before_category", array($ctype, $category));
		list($ctype, $category) = cmsEventsManager::hook("content_{$ctype['name']}_before_category", array($ctype, $category));

		$items_list_html = '';
		$is_hide_items = !empty($ctype['options']['is_empty_root']) && $slug == 'index';

		// Получаем HTML списка записей
		if (!$is_hide_items){
			$items_list_html = $this->renderItemsList($ctype, $page_url, false, $category['id'], array(), $dataset);
		}

        // кешируем
        cmsModel::cacheResult('current_ctype', $ctype);
        cmsModel::cacheResult('current_ctype_category', $category);

        return $this->cms_template->render('category_view', array(
            'is_frontpage'    => $is_frontpage,
            'is_hide_items'   => $is_hide_items,
            'parent'          => isset($parent) ? $parent : false,
            'slug'            => $slug,
            'ctype'           => $ctype,
            'datasets'        => $datasets,
            'dataset'         => $dataset,
            'current_dataset' => (isset($current_dataset) ? $current_dataset : array()),
            'category'        => $category,
            'subcats'         => $subcats,
            'items_list_html' => $items_list_html,
            'user'            => $this->cms_user
        ), $this->request);

    }

    private function getCtypeByAlias($ctype_name) {

        $mapping = cmsConfig::getControllersMapping();
        if($mapping){
            foreach($mapping as $name=>$alias){
                if ($alias == $ctype_name) {
                    return $name;
                }
            }
        }

        return false;

    }

}
