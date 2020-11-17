<div class="widget_comments_list">
    <?php foreach($items as $item) { ?>

        <?php $author_url = href_to_profile($item['user']); ?>
        <?php $target_url = href_to($item['target_url']) . "#comment_{$item['id']}"; ?>

        <div class="item">
            <?php if ($show_avatars){ ?>
            <div class="image">
                <a href="<?php echo $author_url; ?>" <?php if (!empty($item['user']['is_online'])){ ?>class="peer_online" title="<?php echo LANG_ONLINE; ?>"<?php } else { ?> class="peer_no_online"<?php } ?>>
                    <?php echo html_avatar_image($item['user']['avatar'], 'micro', $item['user']['nickname']); ?>
                </a>
            </div>
            <?php } ?>
            <div class="info">
                <div class="title">
                    <?php if ($item['user_id']) { ?>
                        <a class="author" href="<?php echo $author_url; ?>"><?php html($item['user']['nickname']); ?></a>
                    <?php } else { ?>
                        <span class="author"><?php echo $item['author_name']; ?></span>
                    <?php } ?>
                    &rarr;
                    <a class="subject" href="<?php echo $target_url; ?>"><?php echo html_strip($item['target_title'], 50); ?></a>
                    <span class="date">
                        <?php echo string_date_age_max($item['date_pub'], true); ?>
                    </span>
                    <?php if ($item['is_private']) { ?>
                        <span class="is_private" title="<?php html(LANG_PRIVACY_PRIVATE); ?>"></span>
                    <?php } ?>
                </div>
                <?php if ($show_text) { ?>
                    <div class="text">
                        <?php echo html_clean($item['content_html'], 50); ?>
                    </div>
                <?php } ?>
            </div>
        </div>

    <?php } ?>
</div>