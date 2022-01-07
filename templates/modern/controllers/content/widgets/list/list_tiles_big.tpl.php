<?php
/**
 * Template Name: LANG_WD_CONTENT_LIST_STYLE_TILES_BIG
 * Template Type: widget
 */
?>
<div class="icms-widget__content_list mb-n3 mb-md-n4 content_list tiled row">
    <?php foreach($items as $item) { ?>
        <div class="col-md-6 col-lg-4 mb-3 mb-md-4">
            <div class="icms-content-fields d-flex flex-column h-100">
            <?php foreach($item['fields'] as $field){ ?>
                <div class="field ft_<?php echo $field['type']; ?> f_<?php echo $field['name']; ?> <?php echo $field['options']['wrap_type']; ?>_field"<?php if($field['options']['wrap_width']){ ?> style="width: <?php echo $field['options']['wrap_width']; ?>;"<?php } ?>>
                    <?php if ($field['label_pos'] !== 'none'){ ?>
                        <div class="title_<?php echo $field['label_pos']; ?>">
                            <?php echo $field['title'] . ($field['label_pos']==='left' ? ': ' : ''); ?>
                        </div>
                    <?php } ?>
                    <?php if ($field['name'] === 'title' && $ctype['options']['item_on']){ ?>
                        <h4 class="h5 m-0">
                        <?php if ($item['parent_id']){ ?>
                            <a class="parent_title" href="<?php echo rel_to_href($item['parent_url']); ?>"><?php html($item['parent_title']); ?></a>
                            &rarr;
                        <?php } ?>

                        <?php if (!empty($item['is_private_item'])) { ?>
                            <?php html($item[$field['name']]); ?>
                            <span class="is_private text-secondary" title="<?php html($item['private_item_hint']); ?>">
                                <?php html_svg_icon('solid', 'lock'); ?>
                            </span>
                        <?php } else { ?>
                            <a href="<?php echo href_to($ctype['name'], $item['slug'].'.html'); ?>">
                                <?php html($item[$field['name']]); ?>
                            </a>
                            <?php if ($item['is_private']) { ?>
                                <span class="is_private text-secondary" title="<?php echo LANG_PRIVACY_HINT; ?>">
                                    <?php html_svg_icon('solid', 'lock'); ?>
                                </span>
                            <?php } ?>
                        <?php } ?>
                        </h4>
                    <?php } else { ?>
                        <div class="value">
                            <?php echo $field['html']; ?>
                        </div>
                    <?php } ?>
                </div>
            <?php } ?>

            <?php if (!empty($item['info_bar'])){ ?>
                <div class="info_bar p-0 bg-transparent border-0 mt-auto">
                    <?php foreach($item['info_bar'] as $bar){ ?>
                        <div class="mr-2 bar_item <?php echo !empty($bar['css']) ? $bar['css'] : ''; ?>" title="<?php html(!empty($bar['title']) ? $bar['title'] : ''); ?>">
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
        </div>
    <?php } ?>
</div>