<?php

    $list_header = empty($ctype['labels']['list']) ? $ctype['title'] : $ctype['labels']['list'];
    $page_header = isset($category['title']) ? $category['title'] : $list_header;
    $rss_query = !empty($category['id']) ? "?category={$category['id']}" : '';

    $base_url = $ctype['name'];
    $base_ds_url = href_to_rel($ctype['name']) . '%s' . (isset($category['slug']) ? '/'.$category['slug'] : '');

    if (!$is_frontpage){

		$seo_title = false;
		if (!empty($ctype['seo_title'])){ $seo_title = $ctype['seo_title']; }
		if (!empty($category['seo_title'])){ $seo_title = $category['seo_title']; }
		if (!$seo_title) { $seo_title = $page_header; }
        if (!empty($current_dataset['title'])){ $seo_title .= ' · '.$current_dataset['title']; }
        if (!empty($current_dataset['seo_title'])){ $seo_title = $current_dataset['seo_title']; }

        $this->setPageTitle($seo_title);

        if (!empty($ctype['seo_keys'])){ $this->setPageKeywords($ctype['seo_keys']); }
        if (!empty($ctype['seo_desc'])){ $this->setPageDescription($ctype['seo_desc']); }
        if (!empty($category['seo_keys'])){ $this->setPageKeywords($category['seo_keys']); }
        if (!empty($category['seo_desc'])){ $this->setPageDescription($category['seo_desc']); }
        if (!empty($current_dataset['seo_keys'])){ $this->setPageKeywords($current_dataset['seo_keys']); }
        if (!empty($current_dataset['seo_desc'])){ $this->setPageDescription($current_dataset['seo_desc']); }

        $meta_item = !empty($category['id']) ? $category : (!empty($current_dataset['id']) ? $current_dataset : array());

        $this->setPageKeywordsItem($meta_item)->setPageDescriptionItem($meta_item)->setPageTitleItem($meta_item);

    }

    if ($ctype['options']['list_on'] && !$request->isInternal() && !$is_frontpage){
        $this->addBreadcrumb($list_header, href_to($base_url));
    }

    if (isset($category['path']) && $category['path']){
        foreach($category['path'] as $c){
            $this->addBreadcrumb($c['title'], href_to($base_url, $c['slug']));
        }
    }

    if (cmsUser::isAllowed($ctype['name'], 'add')) {

        if (!$category['id'] || $user->isInGroups($category['allow_add'])){

            $href = href_to($ctype['name'], 'add', isset($category['path']) ? $category['id'] : '');

            $this->addToolButton(array(
                'class' => 'add',
                'title' => sprintf(LANG_CONTENT_ADD_ITEM, $ctype['labels']['create']),
                'href'  => $href
            ));

        }

    }

    if ($ctype['is_cats']){
        if (cmsUser::isAllowed($ctype['name'], 'add_cat')) {
            $this->addToolButton(array(
                'class' => 'folder_add',
                'title' => LANG_ADD_CATEGORY,
                'href'  => href_to($ctype['name'], 'addcat', $category['id'])
            ));
        }

        if ($category['id']){

            if (cmsUser::isAllowed($ctype['name'], 'edit_cat')) {
                $this->addToolButton(array(
                    'class' => 'folder_edit',
                    'title' => LANG_EDIT_CATEGORY,
                    'href'  => href_to($ctype['name'], 'editcat', $category['id'])
                ));
            }
            if (cmsUser::isAllowed($ctype['name'], 'delete_cat')) {
                $this->addToolButton(array(
                    'class' => 'folder_delete',
                    'title' => LANG_DELETE_CATEGORY,
                    'href'  => href_to($ctype['name'], 'delcat', $category['id']),
                    'onclick' => "if(!confirm('".LANG_DELETE_CATEGORY_CONFIRM."')){ return false; }"
                ));
            }

        }
    }

    if (cmsUser::isAdmin()){
        $this->addToolButton(array(
            'class' => 'page_gear',
            'title' => sprintf(LANG_CONTENT_TYPE_SETTINGS, mb_strtolower($ctype['title'])),
            'href'  => href_to('admin', 'ctypes', array('edit', $ctype['id']))
        ));
    }

?>

<?php if ($page_header && !$request->isInternal() && !$is_frontpage){  ?>
    <?php if (!empty($list_styles)){ ?>
        <div class="content_list_styles">
            <?php foreach ($list_styles as $list_style) { ?>
                <a rel="nofollow" href="<?php echo $list_style['url']; ?>" class="style_switch <?php echo $list_style['class']; ?>">
                    <?php echo $list_style['title']; ?>
                </a>
            <?php } ?>
        </div>
    <?php } ?>
    <h1>
        <?php echo $page_header; ?>
        <?php if (!empty($ctype['options']['is_rss']) && $this->controller->isControllerEnabled('rss')){ ?>
            <a class="inline_rss_icon" title="RSS" href="<?php echo href_to('rss', 'feed', $ctype['name']) . $rss_query; ?>"></a>
        <?php } ?>
    </h1>
<?php } ?>

<?php if ($datasets && !$is_hide_items){
    $this->renderAsset('ui/datasets-panel', array(
        'datasets'        => $datasets,
        'dataset_name'    => $dataset,
        'current_dataset' => $current_dataset,
        'ds_prefix'       => '-',
        'base_ds_url'     => rel_to_href($base_ds_url)
    ));
} ?>

<?php if (!empty($category['description'])){?>
    <div class="category_description"><?php echo $category['description']; ?></div>
<?php } ?>

<?php if ($subcats && $ctype['is_cats'] && !empty($ctype['options']['is_show_cats'])){ ?>
    <div class="gui-panel content_categories<?php if (count($subcats)>8){ ?> categories_small<?php } ?>">
        <ul class="<?php echo $ctype['name'];?>_icon">
            <?php foreach($subcats as $c){ ?>

            <?php
                $is_ds_view = empty($current_dataset['cats_view']) || in_array($c['id'], $current_dataset['cats_view']);
                $is_ds_hide = !empty($current_dataset['cats_hide']) && in_array($c['id'], $current_dataset['cats_hide']);
            ?>

                <li class="<?php echo str_replace('/', '-', $c['slug']);?>">
                    <a href="<?php echo href_to($base_url . (($dataset && $is_ds_view && !$is_ds_hide) ? '-'.$dataset : ''), $c['slug']); ?>"><?php echo $c['title']; ?></a>
                </li>
            <?php } ?>
        </ul>
    </div>
<?php } ?>

<?php echo $items_list_html; ?>

<?php $hooks_html = cmsEventsManager::hookAll("content_{$ctype['name']}_items_html", array('category_view', $ctype, $category, $current_dataset)); ?>
<?php if ($hooks_html) { ?>
    <div class="sub_items_list">
        <?php echo html_each($hooks_html); ?>
    </div>
<?php } ?>
