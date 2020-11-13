<?php

    if (!empty($ctype['seo_keys'])){ $this->setPageKeywords($ctype['seo_keys']); }
    if (!empty($ctype['seo_desc'])){ $this->setPageDescription($ctype['seo_desc']); }
    if (!empty($item['seo_keys'])){ $this->setPageKeywords($item['seo_keys']); }
    if (!empty($item['seo_desc'])){ $this->setPageDescription($item['seo_desc']); }
    if (!empty($seo_keys)){ $this->setPageKeywords($seo_keys); }
    if (!empty($seo_desc)){ $this->setPageDescription($seo_desc); }

	$this->setPageTitle($seo_title);

    if ($ctype['options']['list_on']){
        $list_header = empty($ctype['labels']['list']) ? $ctype['title'] : $ctype['labels']['list'];
        $this->addBreadcrumb($list_header, href_to($ctype['name']));
    }

    if (isset($item['category']['path'])){
        $base_url = ($this->site_config->ctype_default && in_array($ctype['name'], $this->site_config->ctype_default)) ? '' : $ctype['name'];
        foreach($item['category']['path'] as $c){
            $this->addBreadcrumb($c['title'], href_to($base_url, $c['slug']));
        }
    }

    $this->addBreadcrumb($item['title'], href_to($ctype['name'], $item['slug'] . '.html'));
    $this->addBreadcrumb($child_ctype['title']);

    if (!empty($item['is_approved'])){
        if ($childs && !empty($childs['to_add'])){
            foreach($childs['to_add'] as $rel){
                if($rel['child_ctype_name'] == $child_ctype['name']){
                    $this->addToolButton(array(
                        'class' => 'add',
                        'title' => sprintf(LANG_CONTENT_ADD_ITEM, $rel['child_labels']['create']),
                        'href'  => href_to($rel['child_ctype_name'], 'add') . "?parent_{$ctype['name']}_id={$item['id']}"
                    ));
                }
            }
        }
    }

    if (!empty($childs['tabs']) && $relation['layout'] == 'tab'){

        $this->addMenuItem('item-menu', array(
            'title' => !empty($ctype['labels']['relations_tab_title']) ? $ctype['labels']['relations_tab_title'] : string_ucfirst($ctype['labels']['one']),
            'url'   => href_to($ctype['name'], $item['slug'] . '.html')
        ));

        $this->addMenuItems('item-menu', $childs['tabs']);

    }

?>

<?php if ($relation['layout'] == 'tab') { ?>
    <h1>
        <?php html($item['title']); ?> / <span><?php echo $relation['title']; ?></span>
        <?php if (!empty($current_dataset['title']) && $dataset){ ?><span> / <?php echo $current_dataset['title']; ?></span><?php } ?>
        <?php if ($item['is_private'] == 1) { ?>
            <span class="is_private" title="<?php html(LANG_PRIVACY_PRIVATE); ?>"></span>
        <?php } ?>
    </h1>
    <?php if ($item['parent_id']){ ?>
        <h2 class="parent_title item_<?php echo $item['parent_type']; ?>_title">
            <a href="<?php echo rel_to_href($item['parent_url']); ?>"><?php html($item['parent_title']); ?></a>
        </h2>
    <?php } ?>
<?php } ?>

<?php if ($relation['layout'] == 'hidden') { ?>
    <h1>
        <a href="<?php echo href_to($ctype['name'], $item['slug'] . '.html'); ?>"><?php html($item['title']); ?></a> /
        <?php echo html($child_ctype['title']); ?>
    </h1>
<?php } ?>

<div id="content_item_tabs">
    <div class="tabs-menu">
        <?php $this->menu('item-menu', true, 'tabbed'); ?>
    </div>
</div>
<?php
    if (!empty($toolbar_html)) {
        echo html_each($toolbar_html);
    }
?>
<?php if (!empty($datasets)){
    $this->renderAsset('ui/datasets-panel', array(
        'datasets'        => $datasets,
        'dataset_name'    => $dataset,
        'current_dataset' => $current_dataset,
        'base_ds_url'     => rel_to_href($base_ds_url)
    ));
} ?>
<?php echo $html;
