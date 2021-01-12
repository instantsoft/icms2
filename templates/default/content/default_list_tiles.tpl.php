<?php
/**
 * Template Name: LANG_CP_LISTVIEW_STYLE_TILES
 * Template Type: content
 */
    if( $ctype['options']['list_show_filter'] ) {
        $this->renderAsset('ui/filter-panel', array(
            'css_prefix'   => $ctype['name'],
            'page_url'     => $page_url,
            'fields'       => $fields,
            'props_fields' => $props_fields,
            'props'        => $props,
            'filters'      => $filters,
            'ext_hidden_params' => $ext_hidden_params,
            'is_expanded'  => $ctype['options']['list_expand_filter']
        ));
    }
?>

<?php if ($items){ ?>

    <div class="content_list tiled <?php echo $ctype['name']; ?>_list">

        <?php $columns = 3; $index = 1; ?>

        <?php foreach($items as $item){ ?>

            <?php $stop = 0; ?>

            <div class="tile <?php echo $ctype['name']; ?>_list_item<?php if (!empty($item['is_vip'])){ ?> is_vip<?php } ?>">

                <?php if (!empty($item['fields']['photo'])){ ?>
                    <?php $preset = $fields['photo']['options']['size_teaser']; ?>
                    <div class="photo" style="background-image: url(<?php echo html_image_src((empty($item['is_private_item']) ? $item['photo'] : default_images('private', $preset)), $preset, true); ?>);">
                        <?php if (!empty($fields['date_pub']['is_in_list'])){ ?>
                            <div class="note<?php if(!empty($item['is_new'])){ ?> highlight_new<?php } ?>" title="<?php echo $fields['date_pub']['title']; ?>">
                                <?php echo $fields['date_pub']['handler']->parse( $item['date_pub'] ); ?>
                            </div>
                        <?php } ?>
                        <?php if (empty($item['is_private_item'])) { ?>
                            <a href="<?php echo href_to($ctype['name'], $item['slug'].'.html'); ?>">
                                <?php echo html_image($item['photo'], $preset, $item['title']); ?>
                            </a>
                        <?php } ?>
                        <?php unset($item['fields']['photo']); ?>
                    </div>
                <?php } ?>

                <div class="fields">

                <?php foreach($item['fields'] as $field){ ?>

                    <?php if ($stop === 2) { break; } ?>

                    <div class="field ft_<?php echo $field['type']; ?> f_<?php echo $field['name']; ?>">

                        <?php if ($field['label_pos'] != 'none'){ ?>
                            <div class="title_<?php echo $field['label_pos']; ?>">
                                <?php echo $field['title'] . ($field['label_pos']=='left' ? ': ' : ''); ?>
                            </div>
                        <?php } ?>

                        <?php if ($field['name'] == 'title' && $ctype['options']['item_on']){ ?>
                            <h2 class="value">
                                <?php if ($item['parent_id']){ ?>
                                    <a class="parent_title" href="<?php echo rel_to_href($item['parent_url']); ?>"><?php html($item['parent_title']); ?></a>
                                    &rarr;
                                <?php } ?>
                                <?php if (!empty($item['is_private_item'])) { $stop++; ?>
                                    <?php html($item[$field['name']]); ?> <span class="is_private" title="<?php html($item['private_item_hint']); ?>"></span>
                                <?php } else { ?>
                                    <a class="title" href="<?php echo href_to($ctype['name'], $item['slug'].'.html'); ?>">
                                        <?php html($item[$field['name']]); ?>
                                    </a>
                                    <?php if ($item['is_private']) { ?>
                                        <span class="is_private" title="<?php html(LANG_PRIVACY_HINT); ?>"></span>
                                    <?php } ?>
                                <?php } ?>
                            </h2>
                        <?php } else { ?>
                            <div class="value">
                                <?php if (!empty($item['is_private_item'])) { ?>
                                    <div class="private_field_hint"><?php echo $item['private_item_hint']; ?></div>
                                <?php } else { ?>
                                    <?php echo $field['html']; ?>
                                <?php } ?>
                            </div>
                        <?php } ?>

                    </div>

                <?php } ?>

                </div>

                <?php if (!empty($item['info_bar'])){ ?>
                    <div class="info_bar">
                        <?php foreach($item['info_bar'] as $bar){ ?>
                            <div class="bar_item <?php echo !empty($bar['css']) ? $bar['css'] : ''; ?>" title="<?php html(!empty($bar['title']) ? $bar['title'] : ''); ?>">
                                <?php if (!empty($bar['href'])){ ?>
                                    <a href="<?php echo $bar['href']; ?>"><?php echo $bar['html']; ?></a>
                                <?php } else { ?>
                                    <?php echo $bar['html']; ?>
                                <?php } ?>
                            </div>
                        <?php } ?>
                    </div>
                <?php } ?>

            </div>

            <?php if ($index % $columns == 0) { ?>
                <div class="clear"></div>
            <?php } ?>

        <?php $index++; } ?>

    </div>

    <?php if ($perpage < $total) { ?>
        <?php echo html_pagebar($page, $perpage, $total, $page_url, array_merge($filters, $ext_hidden_params)); ?>
    <?php } ?>

<?php } else {

    if(!empty($ctype['labels']['many'])){
        echo sprintf(LANG_TARGET_LIST_EMPTY, $ctype['labels']['many']);
    } else {
        echo LANG_LIST_EMPTY;
    }

}
