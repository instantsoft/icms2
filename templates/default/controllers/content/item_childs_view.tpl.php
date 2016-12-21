<?php

    if (!empty($ctype['seo_keys'])){ $this->setPageKeywords($ctype['seo_keys']); }
    if (!empty($ctype['seo_desc'])){ $this->setPageDescription($ctype['seo_desc']); }
    if (!empty($item['seo_keys'])){ $this->setPageKeywords($item['seo_keys']); }
    if (!empty($item['seo_desc'])){ $this->setPageDescription($item['seo_desc']); }
    if (!empty($seo_keys)){ $this->setPageKeywords($seo_keys); }
    if (!empty($seo_desc)){ $this->setPageDescription($seo_desc); }

	$this->setPageTitle($seo_title);

    $base_url = $ctype['name'];

    if ($ctype['options']['list_on']){
        $list_header = empty($ctype['labels']['list']) ? $ctype['title'] : $ctype['labels']['list'];
        $this->addBreadcrumb($list_header, href_to($base_url));
    }

    if (isset($item['category'])){
        foreach($item['category']['path'] as $c){
            $this->addBreadcrumb($c['title'], href_to($base_url, $c['slug']));
        }
    }

    $this->addBreadcrumb($item['title'], href_to($ctype['name'], $item['slug'] . '.html'));
    $this->addBreadcrumb($child_ctype['title']);

    $user = cmsUser::getInstance();

    $this->addToolButton(array(
        'class' => 'add',
        'title' => sprintf(LANG_CONTENT_ADD_ITEM, $child_ctype['labels']['create']),
        'href'  => href_to($child_ctype['name'], 'add') . "?parent_{$ctype['name']}_id={$item['id']}"
    ));

    if (!empty($childs['tabs'])){

        $this->addMenuItem('item-menu', array(
            'title' => mb_convert_case($ctype['labels']['one'], MB_CASE_TITLE, 'UTF-8'),
            'url' => href_to($ctype['name'], $item['slug'] . '.html')
        ));

        foreach($childs['tabs'] as $child_ctype_name => $title){
            $this->addMenuItems('item-menu', $childs['tabs']);
        }

    }
?>

<h1>
    <?php html($item['title']); ?>
    <?php if ($item['is_private']) { ?>
        <span class="is_private" title="<?php html(LANG_PRIVACY_PRIVATE); ?>"></span>
    <?php } ?>
</h1>
<?php if ($item['parent_id']){ ?>
    <h2 class="parent_title item_<?php echo $item['parent_type']; ?>_title">
        <a href="<?php echo rel_to_href($item['parent_url']); ?>"><?php html($item['parent_title']); ?></a>
    </h2>
<?php } ?>

<div id="content_item_tabs">
    <div class="tabs-menu">
        <?php $this->menu('item-menu', true, 'tabbed'); ?>
    </div>
</div>

<?php echo $html; ?>