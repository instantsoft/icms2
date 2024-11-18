<?php
/**
 * Template Name: LANG_WD_CONTENT_LIST_STYLE_ALBUMS
 * Template Type: widget
 */
?>
<div class="icms-widget__content_list mb-n3 mb-md-n4 content_list <?php echo $ctype['name']; ?>_list tiled row">
    <?php foreach($items as $item) { ?>

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

        <div class="icms-photo-album <?php echo $ctype['name']; ?>_list_item col-lg-6">
            <div class="icms-bg__cover icms-photo-album__cover position-relative embed-responsive embed-responsive-16by9"<?php if ($image_paths){ ?> style="background-image: url(<?php echo html_image_src($image_paths, $ctype['photos_options']['preset_small'], true); ?>);"<?php } ?>>

                <div class="position-absolute btn-dark btn-sm icms-photo-album__note">
                    <?php echo html_spellcount($item['photos_count'], LANG_PHOTOS_PHOTO_SPELLCOUNT); ?>
                    <?php if (!empty($item['is_public']) && !empty($fields['is_public']['is_in_list'])) { ?>
                        / <span><?php echo LANG_PHOTOS_PUBLIC_ALBUM; ?></span>
                    <?php } ?>
                </div>

                <div class="icms-photo-album__header">
                    <?php if (!empty($item['fields']['title'])) { ?>
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
                <div class="my-2 field ft_<?php echo $field['type']; ?> f_<?php echo $field['name']; ?> <?php html($field['options']['wrap_style'].' '.$field['options']['wrap_style_list']); ?>">
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
                <div class="info_bar icms-photo-album__info_bar">
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