<div class="widget_comments_list">
    <?php foreach($items as $entry) { ?>

        <?php $author_url = href_to_profile($entry['user']); ?>
        <?php $target_url = href_to($entry['target_url']) . "#comment_{$entry['id']}"; ?>

        <div class="media mb-3 comment" >

            <div class="media-body">

                <h4 class="d-inline-block h6 mb-2">
                    <?php if ($show_avatars){ ?>
                        <span class="mr-2">
                            <?php if ($entry['user_id']) { ?>
                                <a href="<?php echo $author_url; ?>" class="icms-user-avatar <?php if (!empty($entry['user']['is_online'])){ ?>peer_online<?php } else { ?>peer_no_online<?php } ?>">
                                    <?php echo html_avatar_image($entry['user']['avatar'], 'micro', $entry['user']['nickname']); ?>
                                </a>
                            <?php } else { ?>
                                <span class="icms-user-avatar">
                                    <?php echo html_avatar_image($entry['user']['avatar'], 'micro', $entry['user']['nickname']); ?>
                                </span>
                            <?php } ?>
                        </span>
                    <?php } ?>
                    <?php if ($entry['user_id']) { ?>
                        <a href="<?php echo $author_url; ?>" class="user"><?php echo $entry['user']['nickname']; ?></a>
                    <?php } else { ?>
                        <span class="guest_name user"><?php echo $entry['author_name']; ?></span>
                    <?php } ?>
                    <?php if ($entry['target_title']) { ?>
                        &rarr;
                        <a class="subject" href="<?php echo $target_url; ?>">
                            <?php html($entry['target_title']); ?>
                        </a>
                    <?php } ?>
                </h4>

                <?php if ($show_text) { ?>
                    <div class="icms-comment-html__widget text-break">
                        <?php if (!empty($entry['disallow_clean_content'])) { ?>
                            <?php echo $entry['content_html']; ?>
                        <?php } else { ?>
                            <?php echo string_short($entry['content_html'], 70, '...', 'w'); ?>
                        <?php } ?>
                    </div>
                <?php } ?>
                <?php if (empty($entry['hide_date'])) { ?>
                    <div class="text-muted small mt-2">
                        <?php html_svg_icon('solid', 'history'); ?>
                        <span>
                            <?php echo string_date_age_max($entry['date_pub'], true); ?>
                        </span>
                        <?php if ($entry['date_last_modified']){ ?>
                            <span data-toggle="tooltip" data-placement="top" class="date_last_modified ml-2" title="<?php echo LANG_CONTENT_EDITED.' '.strip_tags(html_date_time($entry['date_last_modified'])); ?>">
                                <?php html_svg_icon('solid', 'pen'); ?>
                            </span>
                        <?php } ?>
                        <?php if ($entry['is_private']) { ?>
                            <span class="is_private text-secondary" title="<?php html(LANG_PRIVACY_PRIVATE); ?>">
                                <?php html_svg_icon('solid', 'lock'); ?>
                            </span>
                        <?php } ?>
                    </div>
                <?php } ?>
            </div>

        </div>

    <?php } ?>
</div>