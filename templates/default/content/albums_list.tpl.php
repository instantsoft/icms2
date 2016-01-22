<?php
    if( $ctype['options']['list_show_filter'] ) {
        $this->renderAsset('ui/filter-panel', array(
            'css_prefix' => $ctype['name'],
            'page_url' => $page_url,
            'fields' => $fields,
            'props_fields' => $props_fields,
            'props' => $props,
            'filters' => $filters,
            'is_expanded' => $ctype['options']['list_expand_filter']
        ));
    }
?>

<?php if ($items){ ?>

    <div class="content_list tiled <?php echo $ctype['name']; ?>_list">

        <?php $columns = 3; $index = 1; ?>

        <?php foreach($items as $item){ ?>

            <?php
                $item['ctype'] = $ctype;
                $is_private    = $item['is_private'] && $hide_except_title && !$item['user']['is_friend'];
                $stop = 0;
            ?>

            <div class="tile <?php echo $ctype['name']; ?>_list_item">

                <div class="photo">
                    <div class="note">
                        <?php echo html_spellcount($item['photos_count'], LANG_PHOTOS_PHOTO_SPELLCOUNT); ?>
						<?php if ($item['is_public']) { ?>
							/ <span><?php echo LANG_PHOTOS_PUBLIC_ALBUM; ?></span>
						<?php } ?>
                    </div>
                    <?php if ($is_private) { ?>
                        <?php echo html_image(default_images('private', 'normal'), 'normal', $item['title']); ?>
                    <?php } else { ?>
                        <a href="<?php echo href_to($ctype['name'], $item['slug'].'.html'); ?>">
                            <?php if (!empty($item['cover_image'])){ ?>
                                <?php echo html_image($item['cover_image'], 'normal', $item['title']); ?>
                                <?php unset($item['cover_image']); ?>
                            <?php } ?>
                        </a>
                    <?php } ?>
                    <?php unset($item['cover_image']); ?>
                </div>

                <div class="fields">

                <?php foreach($fields as $field){ ?>

                    <?php if ($stop === 2) { break; } ?>
                    <?php if (empty($item[$field['name']])) { continue; } ?>
                    <?php if ($field['is_system']) { continue; } ?>
                    <?php if (!$field['is_in_list']) { continue; } ?>
                    <?php if ($field['groups_read'] && !$user->isInGroups($field['groups_read'])) { continue; } ?>

                    <?php
                        if (!isset($field['options']['label_in_list'])) {
                            $label_pos = 'none';
                        } else {
                            $label_pos = $field['options']['label_in_list'];
                        }
                    ?>

                    <div class="field ft_<?php echo $field['type']; ?> f_<?php echo $field['name']; ?>">

                        <?php if ($label_pos != 'none'){ ?>
                            <div class="title_<?php echo $label_pos; ?>"><?php echo $field['title'] . ($label_pos=='left' ? ': ' : ''); ?></div>
                        <?php } ?>

                        <div class="value">
                            <?php if ($field['name'] == 'title' && $ctype['options']['item_on']){ ?>

                                <?php if ($item['parent_id']){ ?>
                                    <a class="parent_title" href="<?php echo href_to($item['parent_url']); ?>"><?php echo htmlspecialchars($item['parent_title']); ?></a>
                                    &rarr;
                                <?php } ?>

                                <?php if ($is_private) { ?>
                                    <?php html($item[$field['name']]); ?> <span class="is_private" title="<?php html(LANG_PRIVACY_PRIVATE); ?>"></span>
                                <?php } else { ?>
                                    <a class="title" href="<?php echo href_to($ctype['name'], $item['slug'].'.html'); ?>"><?php html($item[$field['name']]); ?></a>
                                    <?php if ($item['is_private']) { ?>
                                        <span class="is_private" title="<?php html(LANG_PRIVACY_PRIVATE); ?>"></span>
                                    <?php } ?>
                                <?php } ?>

                            <?php } else { ?>

                               <?php if ($is_private) { $stop++; ?>
                                    <!--noindex--><div class="private_field_hint"><?php echo LANG_PRIVACY_PRIVATE_HINT; ?></div><!--/noindex-->
                               <?php } else { ?>
                                    <?php echo $field['handler']->setItem($item)->parseTeaser($item[$field['name']]); ?>
                               <?php } ?>

                            <?php } ?>
                        </div>

                    </div>

                <?php } ?>

                </div>

                <?php
                    $is_tags = $ctype['is_tags'] &&
                            !empty($ctype['options']['is_tags_in_list']) &&
                            $item['tags'];
                ?>

                <?php if ($is_tags){ ?>
                    <div class="tags_bar">
                        <?php echo html_tags_bar($item['tags']); ?>
                    </div>
                <?php } ?>

                <?php
                    $show_bar = $ctype['is_rating'] ||
                                $fields['date_pub']['is_in_list'] ||
                                $fields['user']['is_in_list'] ||
                                !$item['is_approved'];
                ?>

                <?php if ($show_bar){ ?>
                    <div class="info_bar">
                        <?php if ($ctype['is_rating']){ ?>
                            <div class="bar_item bi_rating">
                                <?php echo $item['rating_widget']; ?>
                            </div>
                        <?php } ?>
                        <?php if ($fields['user']['is_in_list']){ ?>
                            <div class="bar_item bi_user" title="<?php echo $fields['user']['title']; ?>">
                                <?php echo $fields['user']['handler']->parse( $item['user'] ); ?>
                            </div>
                        <?php } ?>
                        <?php if ($ctype['is_comments']){ ?>
                            <div class="bar_item bi_comments">
                                <?php if ($is_private) { ?>
                                    <?php echo intval($item['comments']); ?>
                                <?php } else { ?>
                                    <a href="<?php echo href_to($ctype['name'], $item['slug'].'.html'); ?>#comments" title="<?php echo LANG_COMMENTS; ?>">
                                        <?php echo intval($item['comments']); ?>
                                    </a>
                                <?php } ?>
                            </div>
                        <?php } ?>
                        <?php if (!$item['is_approved']){ ?>
                            <div class="bar_item bi_not_approved">
                                <?php echo LANG_CONTENT_NOT_APPROVED; ?>
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
        <?php echo html_pagebar($page, $perpage, $total, $page_url, $filters); ?>
    <?php } ?>

<?php } else { echo LANG_LIST_EMPTY; } ?>