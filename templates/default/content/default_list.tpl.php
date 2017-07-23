<?php
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

    <div class="content_list featured <?php echo $ctype['name']; ?>_list">

        <?php foreach($items as $item){ ?>

            <?php $stop = 0; ?>

			<div class="content_list_item <?php echo $ctype['name']; ?>_list_item<?php if (!empty($item['is_vip'])){ ?> is_vip<?php } ?>">

                <?php if (!empty($item['fields']['photo'])){ ?>
                    <div class="photo">
                        <?php if (!empty($item['is_private_item'])) { ?>
                            <?php echo html_image(default_images('private', $fields['photo']['options']['size_teaser']), $fields['photo']['options']['size_teaser'], $item['title']); ?>
                        <?php } else { ?>
                            <a href="<?php echo href_to($ctype['name'], $item['slug'].'.html'); ?>">
                                <?php echo html_image($item['photo'], $fields['photo']['options']['size_teaser'], $item['title']); ?>
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
                                <a href="<?php echo href_to($ctype['name'], $item['slug'].'.html'); ?>"><?php html($item[$field['name']]); ?></a>
                                <?php if ($item['is_private']) { ?>
                                    <span class="is_private" title="<?php echo LANG_PRIVACY_HINT; ?>"></span>
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

                <?php if ($ctype['is_tags'] && !empty($ctype['options']['is_tags_in_list']) &&  $item['tags']){ ?>
                    <div class="tags_bar">
                        <?php echo html_tags_bar($item['tags']); ?>
                    </div>
                <?php } ?>

                <?php
					$show_bar = !empty($item['rating_widget']) ||
								$fields['date_pub']['is_in_list'] ||
								$fields['user']['is_in_list'] ||
								!empty($ctype['options']['hits_on']) ||
								($ctype['is_comments'] && $item['is_comments_on']) ||
								!$item['is_pub'] ||
								!$item['is_approved'];
                ?>

                <?php if($show_bar){ ?>
                    <div class="info_bar">
                        <?php if (!empty($item['rating_widget'])){ ?>
                            <div class="bar_item bi_rating">
                                <?php echo $item['rating_widget']; ?>
                            </div>
                        <?php } ?>
                        <?php if ($fields['date_pub']['is_in_list']){ ?>
                            <div class="bar_item bi_date_pub" title="<?php echo $fields['date_pub']['title']; ?>">
                                <?php echo $fields['date_pub']['handler']->parse( $item['date_pub'] ); ?>
                            </div>
                        <?php } ?>
                        <?php if (!$item['is_pub']){ ?>
                            <div class="bar_item bi_not_pub">
                                <?php echo LANG_CONTENT_NOT_IS_PUB; ?>
                            </div>
                        <?php } ?>
                        <?php if ($fields['user']['is_in_list']){ ?>
                            <div class="bar_item bi_user" title="<?php echo $fields['user']['title']; ?>">
                                <?php echo $fields['user']['handler']->parse( $item['user'] ); ?>
                            </div>
                            <?php if (!empty($item['folder_title'])){ ?>
                                <div class="bar_item bi_folder">
                                    <a href="<?php echo href_to('users', $item['user']['id'], array('content', $ctype['name'], $item['folder_id'])); ?>"><?php echo $item['folder_title']; ?></a>
                                </div>
                            <?php } ?>
                        <?php } ?>
                        <?php if (!empty($ctype['options']['hits_on'])){ ?>
                            <div class="bar_item bi_hits" title="<?php echo LANG_HITS; ?>">
                                <?php echo $item['hits_count']; ?>
                            </div>
                        <?php } ?>
                        <?php if ($ctype['is_comments'] && $item['is_comments_on']){ ?>
                            <div class="bar_item bi_comments">
                                <?php if (!empty($item['is_private_item'])) { ?>
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

        <?php } ?>

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
