<?php
$this->addTplCSSFromContext('controllers/photos/styles');

if( $ctype['options']['list_show_filter'] ) {
    $this->renderAsset('ui/filter-panel', [
        'css_prefix'   => $ctype['name'],
        'page_url'     => $page_url,
        'fields'       => $fields,
        'props_fields' => $props_fields,
        'props'        => $props,
        'filters'      => $filters,
        'ext_hidden_params' => $ext_hidden_params,
        'is_expanded'  => $ctype['options']['list_expand_filter']
    ]);
}
?>

<?php if (!$items){ ?>
    <p class="alert alert-info mt-4 alert-list-empty">
        <?php if(!empty($ctype['labels']['many'])){ ?>
            <?php echo sprintf(LANG_TARGET_LIST_EMPTY, $ctype['labels']['many']); ?>
        <?php } else { ?>
            <?php echo LANG_LIST_EMPTY; ?>
        <?php } ?>
    </p>
<?php return; } ?>

<div class="content_list tiled <?php echo $ctype['name']; ?>_list row mt-3 mt-md-4">

    <?php foreach($items as $item){ ?>

        <?php
            $image_paths = '';
            if (!empty($item['cover_image']) && !empty($fields['cover_image']['is_in_list'])){
                if (!empty($item['is_private_item'])) {
                    $image_paths = default_images('private', $ctype['photos_options']['preset_small']);
                } else {
                    $image_paths = $item['cover_image'];
                }
            }
        ?>

        <div class="icms-photo-album <?php echo $ctype['name']; ?>_list_item col-md-6 col-lg-4">
            <div class="icms-bg__cover icms-photo-album__cover position-relative embed-responsive embed-responsive-16by9"<?php if ($image_paths){ ?> style="background-image: url(<?php echo html_image_src($image_paths, $ctype['photos_options']['preset_small'], true); ?>);"<?php } ?>>

                <?php if (!empty($this->menus['list_actions_menu'])){ ?>
                    <div class="dropdown position-absolute text-white">
                        <button class="btn text-white" type="button" data-toggle="dropdown">
                            <?php html_svg_icon('solid', 'ellipsis-v'); ?>
                        </button>
                        <div class="dropdown-menu dropdown-menu-right">
                            <?php foreach($this->menus['list_actions_menu'] as $menu){ ?>
                                <a class="dropdown-item <?php echo isset($menu['options']['class']) ? $menu['options']['class'] : ''; ?>" href="<?php echo string_replace_keys_values($menu['url'], $item); ?>" title="<?php html($menu['title']); ?>">
                                    <?php echo $menu['title']; ?>
                                </a>
                            <?php } ?>
                        </div>
                    </div>
                <?php } ?>

                <div class="position-absolute btn-dark btn-sm icms-photo-album__note">
                    <?php echo html_spellcount($item['photos_count'], LANG_PHOTOS_PHOTO_SPELLCOUNT); ?>
                    <?php if (!empty($item['is_public']) && !empty($fields['is_public']['is_in_list'])) { ?>
                        / <span><?php echo LANG_PHOTOS_PUBLIC_ALBUM; ?></span>
                    <?php } ?>
                </div>

                <div class="icms-photo-album__header">
                    <?php if (!empty($fields['title']['is_in_list'])) { ?>
                        <div class="text-truncate">
                            <?php if ($item['parent_id']){ ?>
                                <?php html($item['parent_title']); ?> &rarr;
                            <?php } ?>
                            <?php if (!empty($item['is_private_item']) || empty($ctype['options']['item_on'])) { ?>
                                <?php html($item['title']); ?>
                                <?php if (!empty($item['is_private_item'])) { ?>
                                    <span class="is_private text-secondary" title="<?php html($item['private_item_hint']); ?>">
                                        <?php html_svg_icon('solid', 'lock'); ?>
                                    </span>
                                <?php } ?>
                            <?php } else { ?>
                                <a class="stretched-link" href="<?php echo href_to($ctype['name'], $item['slug'].'.html'); ?>">
                                    <?php html($item['title']); ?>
                                </a>
                                <?php if ($item['is_private']) { ?>
                                    <span class="is_private text-secondary" title="<?php echo LANG_PRIVACY_HINT; ?>">
                                        <?php html_svg_icon('solid', 'lock'); ?>
                                    </span>
                                <?php } ?>
                            <?php } ?>
                        </div>
                    <?php } ?>
                    <?php if (!empty($item['fields']['content']['html'])) { ?>
                        <div class="icms-photo-album__header-desc">
                            <?php echo $item['fields']['content']['html']; ?>
                        </div>
                    <?php } ?>
                </div>
                <?php unset($item['fields']['cover_image'], $item['fields']['content'], $item['fields']['is_public'], $item['fields']['title']); ?>
            </div>
            <?php foreach($item['fields'] as $field){ ?>
                <div class="my-2 field ft_<?php echo $field['type']; ?> f_<?php echo $field['name']; ?> <?php echo $field['options']['wrap_style']; ?>">
                    <?php if ($field['label_pos'] !== 'none'){ ?>
                        <div class="title_<?php echo $field['label_pos']; ?>">
                            <?php echo string_replace_svg_icons($field['title']) . ($field['label_pos']=='left' ? ': ' : ''); ?>
                        </div>
                    <?php } ?>
                    <div class="value">
                        <?php echo $field['html']; ?>
                    </div>
                </div>
            <?php } ?>

            <?php if (!empty($item['show_tags'])){ ?>
                <div class="tags_bar my-2">
                    <?php echo html_tags_bar($item['tags'], 'content-'.$ctype['name'], 'btn btn-outline-secondary btn-sm icms-btn-tag', ''); ?>
                </div>
            <?php } ?>

            <?php if (!empty($item['info_bar'])){ ?>
                <div class="info_bar">
                    <?php foreach($item['info_bar'] as $bar){ ?>
                        <div class="bar_item <?php echo !empty($bar['css']) ? $bar['css'] : ''; ?>" title="<?php html(!empty($bar['title']) ? $bar['title'] : ''); ?>">
                            <?php if (!empty($bar['icon'])){ ?>
                                <?php html_svg_icon('solid', $bar['icon']); ?>
                            <?php } ?>
                            <?php if (!empty($bar['href'])){ ?>
                                <a class="stretched-link" href="<?php echo $bar['href']; ?>">
                                    <?php echo $bar['html']; ?>
                                </a>
                            <?php } else { ?>
                                <?php echo $bar['html']; ?>
                            <?php } ?>
                        </div>
                    <?php } ?>
                </div>
            <?php } ?>

        </div>

    <?php } ?>

</div>
<?php echo html_pagebar($page, $perpage, $total, $page_url, $filter_query); ?>