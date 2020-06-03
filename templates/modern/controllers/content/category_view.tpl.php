<?php

    if (cmsUser::isAllowed($ctype['name'], 'add')) {

        if (!$category['id'] || $user->isInGroups($category['allow_add'])){

            $href = href_to($ctype['name'], 'add', isset($category['path']) ? $category['id'] : '');

            $this->addToolButton(array(
                'icon' => 'plus-circle',
                'title' => sprintf(LANG_CONTENT_ADD_ITEM, $ctype['labels']['create']),
                'href'  => $href
            ));

        }

    }

    if ($ctype['is_cats']){
        if (cmsUser::isAllowed($ctype['name'], 'add_cat')) {
            $this->addToolButton(array(
                'icon' => 'folder-plus',
                'title' => LANG_ADD_CATEGORY,
                'href'  => href_to($ctype['name'], 'addcat', $category['id'])
            ));
        }

        if ($category['id']){

            if (cmsUser::isAllowed($ctype['name'], 'edit_cat')) {
                $this->addToolButton(array(
                    'icon'  => 'edit',
                    'title' => LANG_EDIT_CATEGORY,
                    'href'  => href_to($ctype['name'], 'editcat', $category['id'])
                ));
            }
            if (cmsUser::isAllowed($ctype['name'], 'delete_cat')) {
                $this->addToolButton(array(
                    'icon' => 'folder-minus',
                    'title' => LANG_DELETE_CATEGORY,
                    'href'  => href_to($ctype['name'], 'delcat', $category['id']),
                    'onclick' => "if(!confirm('".LANG_DELETE_CATEGORY_CONFIRM."')){ return false; }"
                ));
            }

        }
    }

    if (cmsUser::isAdmin()){
        $this->addToolButton(array(
            'icon' => 'cogs',
            'title' => sprintf(LANG_CONTENT_TYPE_SETTINGS, mb_strtolower($ctype['title'])),
            'href'  => href_to('admin', 'ctypes', array('edit', $ctype['id']))
        ));
    }

?>
<?php if ($this->hasPageH1() && !$request->isInternal() && !$is_frontpage){  ?>
    <?php ob_start(); ?>
        <h1>
            <?php $this->pageH1(); ?>
            <?php if (!empty($ctype['options']['is_rss']) && $this->controller->isControllerEnabled('rss')){ ?>
                <sup>
                    <a class="inline_rss_icon d-none d-lg-inline-block" title="RSS" href="<?php echo href_to('rss', 'feed', $ctype['name']) . $rss_query; ?>">
                        <?php html_svg_icon('solid', 'rss'); ?>
                    </a>
                </sup>
            <?php } ?>
        </h1>
        <?php if (!empty($list_styles)){ ?>
            <?php $list_icons_mapping = ['' => 'list', 'featured' => 'newspaper', 'table' => 'table', 'tiles' => 'th']; ?>
            <div class="icms-content-list__styles_btn">
                <?php foreach ($list_styles as $list_style) { ?>
                <a data-toggle="tooltip" data-placement="top" rel="nofollow" href="<?php echo $list_style['url']; ?>" class="btn btn-light btn-responsive icms-content-list__<?php echo $list_style['class']; ?>" title="<?php html($list_style['title']); ?>">
                    <?php html_svg_icon('solid', $list_icons_mapping[$list_style['style']]); ?>
                </a>
                <?php } ?>
            </div>
        <?php } ?>
    <?php $this->addToBlock('before_body', ob_get_clean(), true); ?>
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
    <div class="gui-panel content_categories<?php if (count($subcats)>8 && !$ctype['options']['cover_preset']){ ?> categories_small<?php } ?>">
        <ul class="<?php echo $ctype['name'];?>_icon <?php if($ctype['options']['cover_preset']){ ?>has_cover_preset cover_preset_<?php echo $ctype['options']['cover_preset'];?><?php } ?>">
            <?php foreach($subcats as $c){ ?>

            <?php
                $is_ds_view = empty($current_dataset['cats_view']) || in_array($c['id'], $current_dataset['cats_view']);
                $is_ds_hide = !empty($current_dataset['cats_hide']) && in_array($c['id'], $current_dataset['cats_hide']);
                $img_src  = html_image_src($c['cover'], $ctype['options']['cover_preset'], true);
            ?>

                <li <?php if($img_src){ ?>style="background-image: url(<?php echo $img_src; ?>);"<?php } ?> class="<?php echo str_replace('/', '-', $c['slug']);?> <?php if($img_src){ ?>set_cover_preset<?php } ?>">
                    <a href="<?php echo href_to((($dataset && $is_ds_view && !$is_ds_hide) ? $ctype['name'].'-'.$dataset : $base_url), $c['slug']); ?>">
                        <span><?php echo $c['title']; ?></span>
                    </a>
                </li>
            <?php } ?>
        </ul>
    </div>
<?php } ?>

    <?php $this->block('before_content_items_list_html'); ?>

<?php echo $items_list_html; ?>

<?php if ($hooks_html) { ?>
    <div class="sub_items_list">
        <?php echo html_each($hooks_html); ?>
    </div>
<?php } ?>