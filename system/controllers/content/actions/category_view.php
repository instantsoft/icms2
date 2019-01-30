<?php

class actionContentCategoryView extends cmsAction {

    public function run(){

        // Получаем SLUG категории
        $slug = $this->request->get('slug', '');

        // Текущий набор
        $dataset = $this->request->get('dataset', '');

        // Это вывод на главной?
        $is_frontpage = $this->request->get('is_frontpage', false);

        // Номер страницы
        $page = $this->request->get('page', 1);

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
                return cmsCore::error404();
            } else {
                $this->cms_core->uri_controller_before_remap = $ctype_name;
            }

        } elseif(!$this->cms_core->uri_controller_before_remap && !$dataset && $this->cms_config->ctype_default && $this->cms_config->ctype_default === $ctype_name && $slug !== 'index'){
            $this->redirect(href_to($slug), 301);
        }

        if (!$ctype['options']['list_on']) { return cmsCore::error404(); }

        $category = array('id' => false, 'description' => (!empty($ctype['description']) ? $ctype['description'] : ''));

        $subcats = array();

        if (!$ctype['is_cats'] && $slug != 'index') { return cmsCore::error404(); }

        if ($ctype['is_cats'] && $slug != 'index') {
            $category = $this->model->getCategoryBySLUG($ctype['name'], $slug);
            if (!$category){ return cmsCore::error404(); }
        }

        // Получаем список подкатегорий для текущей
        if ($ctype['is_cats']) {
            $current_cat_id = $category['id'] ? $category['id'] : 1;
            $subcats = $this->model->getSubCategories($ctype['name'], $current_cat_id);
        }

        // Получаем список наборов
        $datasets = $this->getCtypeDatasets($ctype, array(
            'cat_id' => $category['id']
        ));

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
        $current_dataset = array();
        if ($datasets){
            if($dataset && empty($datasets[$dataset])){ return cmsCore::error404(); }
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
        } elseif($dataset){
            return cmsCore::error404();
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
        $this->model->enableHiddenParentsFilter();

        // Формируем базовые URL для страниц
        $base_url = $this->cms_config->ctype_default == $ctype['name'] ? '' : $ctype['name'];

        $page_url = href_to($ctype['name']);

        if($dataset){
            $page_url .= '-'.$dataset;
        }

        if(!empty($category['slug'])){

            $page_url .= '/'.$category['slug'];

            if(!$base_url){
                $page_url = str_replace($ctype['name'].'/', '', $page_url);
            }

        }

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

        $list_styles = array();

        $current_style = '';
        if(!empty($ctype['options']['list_style'])){
            if(is_array($ctype['options']['list_style'])){
                $current_style = $ctype['options']['list_style'][0] ? '_'.$ctype['options']['list_style'][0] : '';
            } else {
                $current_style = '_'.$ctype['options']['list_style'];
            }
        }

        if(!empty($ctype['options']['list_style'])){
            if(is_array($ctype['options']['list_style']) && count($ctype['options']['list_style']) > 1){

                $style_key_name = $ctype['name'].'_ctype_list_style';

                $ctype_list_style_preset = false;

                if(cmsUser::hasCookie($style_key_name)){
                    $ctype_list_style_preset = cmsUser::getCookie($style_key_name, 'string', function ($cookie){ return trim(strip_tags($cookie)); });
                    $ctype_list_style_preset = $ctype_list_style_preset === 'default' ? '' : $ctype_list_style_preset;
                }

                if($this->cms_user->is_logged){
                    $ctype_list_style_preset = cmsUser::getUPS($style_key_name);
                    $ctype_list_style_preset = $ctype_list_style_preset === null ? '' : $ctype_list_style_preset;
                }

                $current_style = $this->request->has('style') ?
                        $this->request->get('style', '') :
                        ($ctype_list_style_preset !== false ? $ctype_list_style_preset : $ctype['options']['list_style'][0]);

                if(!in_array($current_style, $ctype['options']['list_style'])){
                    $current_style = $ctype['options']['list_style'][0];
                }

                // запоминаем стиль в куки
                if(!$this->cms_user->is_logged){
                    cmsUser::setCookie($style_key_name, ($current_style === '' ? 'default' : $current_style), 604800);
                } else {
                    cmsUser::setUPS($style_key_name, $current_style);
                }

                $style_titles = array();
                if(!empty($ctype['options']['list_style_names'])){
                    foreach ($ctype['options']['list_style_names'] as $list_style_names) {
                        $style_titles[$list_style_names['name']] = $list_style_names['value'];
                    }
                }

                foreach ($ctype['options']['list_style'] as $list_style) {
                    $list_styles[] = array(
                        'title' => (isset($style_titles[$list_style]) ? $style_titles[$list_style] : ''),
                        'style' => $list_style,
                        'url'   => $page_url.'?style='.$list_style,
                        'class' => $list_style.($current_style === $list_style ? ' active' : ''),
                    );
                }

                $ctype['options']['raw_list_style'] = $ctype['options']['list_style'];
                $ctype['options']['list_style'] = $current_style;

            }
        }

        $ctype['options']['cover_preset'] = '';
        if(!empty($ctype['options']['cover_sizes']) && !empty($ctype['options']['context_list_cover_sizes'])){
            $cover_key = ltrim($current_style, '_');
            if(array_key_exists($cover_key, $ctype['options']['context_list_cover_sizes'])){
                $ctype['options']['cover_preset'] = $ctype['options']['context_list_cover_sizes'][$cover_key];
            }
        }

        // кешируем
        cmsModel::cacheResult('current_ctype', $ctype);
        cmsModel::cacheResult('current_ctype_category', $category);
        cmsModel::cacheResult('current_ctype_dataset', $current_dataset);

		// Получаем HTML списка записей
		if (!$is_hide_items){
			$items_list_html = $this->renderItemsList($ctype, $page_url, false, $category['id'], array(), $dataset);
		}

        $tpl_file = $this->cms_template->getTemplateFileName('controllers/content/category_view_'.$ctype['name'], true) ?
                'category_view_'.$ctype['name'] : 'category_view';

        $hooks_html = cmsEventsManager::hookAll("content_{$ctype['name']}_items_html", array('category_view', $ctype, $category, $current_dataset));

        $toolbar_html = cmsEventsManager::hookAll('content_toolbar_html', array($ctype['name'], $category, $current_dataset, array()));
        if ($toolbar_html) {
            $this->cms_template->addToBlock('before_body', html_each($toolbar_html));
        }

        $list_header = empty($ctype['labels']['list']) ? $ctype['title'] : $ctype['labels']['list'];
        $page_header = !empty($category['seo_h1']) ? $category['seo_h1'] : (!empty($category['title']) ? $category['title'] : $list_header);
        $rss_query   = !empty($category['id']) ? "?category={$category['id']}" : '';

        $base_ds_url = href_to_rel($ctype['name']) . '%s' . (isset($category['slug']) ? '/'.$category['slug'] : '');

        if (!$is_frontpage){

            $filter_titles = $this->getFilterTitles();

            $seo_title = $seo_desc = $seo_keys = '';

            if (!empty($ctype['seo_title']) && empty($category['title'])){ $seo_title = $ctype['seo_title']; }
            if (!empty($category['seo_title'])){ $seo_title = $category['seo_title']; }
            if (!$seo_title) { $seo_title = $page_header; }
            if (!empty($current_dataset['title'])){ $seo_title .= ' · '.$current_dataset['title']; }
            if (!empty($current_dataset['seo_title'])){ $seo_title = $current_dataset['seo_title']; }
            if (!empty($filter_titles)){ $seo_title .= ', '.implode(', ', $filter_titles); }

            $this->cms_template->setPageTitle($seo_title);

            if (!empty($ctype['seo_keys'])){ $seo_keys = $ctype['seo_keys']; }
            if (!empty($ctype['seo_desc'])){ $seo_desc = $ctype['seo_desc']; }
            if (!empty($category['seo_keys'])){ $seo_keys = $category['seo_keys']; }
            if (!empty($category['seo_desc'])){ $seo_desc = $category['seo_desc']; }
            if (!empty($current_dataset['seo_keys'])){ $seo_keys = $current_dataset['seo_keys']; }
            if (!empty($current_dataset['seo_desc'])){ $seo_desc = $current_dataset['seo_desc']; }

            $this->cms_template->setPageKeywords($seo_keys);
            $this->cms_template->setPageDescription($seo_desc);

            $meta_item = !empty($category['id']) ? $category : (!empty($current_dataset['id']) ? $current_dataset : array());

            $this->cms_template->
                    setPageKeywordsItem($meta_item)->
                    setPageDescriptionItem($meta_item)->
                    setPageTitleItem($meta_item);

        }

        if (empty($ctype['options']['list_off_breadcrumb'])){

            if ($ctype['options']['list_on'] && !$is_frontpage){
                $this->cms_template->addBreadcrumb($list_header, href_to($ctype['name']));
            }

            if (isset($category['path']) && $category['path']){
                foreach($category['path'] as $c){
                    $this->cms_template->addBreadcrumb($c['title'], href_to($base_url, $c['slug']));
                }
            }

        }

        return $this->cms_template->render($tpl_file, array(
            'base_ds_url'     => $base_ds_url,
            'base_url'        => $base_url,
            'rss_query'       => $rss_query,
            'page_header'     => $page_header,
            'list_styles'     => $list_styles,
            'is_frontpage'    => $is_frontpage,
            'is_hide_items'   => $is_hide_items,
            'hooks_html'      => $hooks_html,
            'toolbar_html'    => $toolbar_html,
            'slug'            => $slug,
            'ctype'           => $ctype,
            'datasets'        => $datasets,
            'dataset'         => $dataset,
            'current_dataset' => $current_dataset,
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
