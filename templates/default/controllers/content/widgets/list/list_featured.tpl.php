<?php if ($items){ ?>

    <div class="widget_content_list featured">
        <?php foreach($items as $item) { ?>

            <?php $url = href_to($ctype['name'], $item['slug']) . '.html'; ?>
            <?php $is_first = !isset($is_first); ?>
            <?php $size = $is_first ? 'normal' : 'small'; ?>

            <div class="item <?php if ($is_first) { ?>item-first<?php } ?>">
                <?php if ($image_field && !empty($item[$image_field])) { ?>
                    <?php if ($is_first) { ?>
                        <div class="image">
                            <a style="background-image:url('<?php echo html_image_src($item[$image_field], $size, true); ?>')" href="<?php echo $url; ?>"></a>
                        </div>
                    <?php } else { ?>
                        <div class="image">
                            <a href="<?php echo $url; ?>"><?php echo html_image($item[$image_field], $size); ?></a>
                        </div>
                    <?php } ?>
                <?php } ?>
                <div class="info">
                    <?php if ($is_first && $is_show_details) { ?>
                        <div class="details">
                            <span class="author">
                                <a href="<?php echo href_to('users', $item['user']['id']); ?>"><?php html($item['user']['nickname']); ?></a>
                                <?php if ($item['parent_id']){ ?>
                                    <?php echo LANG_WROTE_IN_GROUP; ?>
                                    <a href="<?php echo href_to($item['parent_url']); ?>"><?php html($item['parent_title']); ?></a>
                                <?php } ?>
                            </span>
                            <span class="date">
                                <?php html(string_date_age_max($item['date_pub'], true)); ?>
                            </span>
                            <?php if($ctype['is_comments']){ ?>
                                <span class="comments">
                                    <a href="<?php echo $url . '#comments'; ?>" title="<?php echo LANG_COMMENTS; ?>"><?php echo intval($item['comments']); ?></a>
                                </span>
                            <?php } ?>
                        </div>
                    <?php } ?>
                    <div class="title">
                        <a href="<?php echo $url; ?>"><?php html($item['title']); ?></a>
                        <?php if ($item['is_private']) { ?>
                            <span class="is_private" title="<?php html(LANG_PRIVACY_PRIVATE); ?>"></span>
                        <?php } ?>
                    </div>
                    <?php if ($teaser_field && !empty($item[$teaser_field])) { ?>
                        <div class="teaser"><?php echo $item[$teaser_field]; ?></div>
                    <?php } ?>
                    <?php if ($is_first) { ?>
                        <div class="read-more">
                            <a href="<?php echo $url; ?>"><?php echo LANG_MORE; ?></a>
                        </div>
                        <?php } ?>
                    <?php if (!$is_first && $is_show_details) { ?>
                        <div class="details">
                            <span class="author">
                                <a href="<?php echo href_to('users', $item['user']['id']); ?>"><?php html($item['user']['nickname']); ?></a>
                                <?php if ($item['parent_id']){ ?>
                                    <?php echo LANG_WROTE_IN_GROUP; ?>
                                    <a href="<?php echo href_to($item['parent_url']); ?>"><?php html($item['parent_title']); ?></a>
                                <?php } ?>
                            </span>
                            <span class="date">
                                <?php html(string_date_age_max($item['date_pub'], true)); ?>
                            </span>
                            <?php if($ctype['is_comments']){ ?>
                                <span class="comments">
                                    <a href="<?php echo $url . '#comments'; ?>" title="<?php echo LANG_COMMENTS; ?>"><?php echo intval($item['comments']); ?></a>
                                </span>
                            <?php } ?>
                        </div>
                    <?php } ?>
                </div>
            </div>

        <?php } ?>
    </div>

<?php } ?>