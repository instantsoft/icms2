<?php
/**
 * Template Name: LANG_WD_CONTENT_LIST_STYLE_COMPACT
 * Template Type: widget
 */

if ($items){ ?>

    <div class="widget_content_list compact">
        <?php foreach($items as $item) { ?>

            <?php
                $url        = href_to($ctype['name'], $item['slug'] . '.html');
                $is_private = $item['is_private'] && $hide_except_title && !$item['user']['is_friend'];
                $image      = (($image_field && !empty($item[$image_field])) ? $item[$image_field] : '');
                if ($is_private) {
                    if($image_field && !empty($item[$image_field])){
                        $image = default_images('private', 'micro');
                    }
                    $url = '';
                }
            ?>

            <div class="item">
                <?php if ($image) { ?>
                    <div class="image">
                        <?php if ($url) { ?>
                            <a href="<?php echo $url; ?>" title="<?php html($item['title']); ?>">
                                <?php echo html_image($image, 'micro', $item['title']); ?>
                            </a>
                        <?php } else { ?>
                            <?php echo html_image($image, 'micro', $item['title']); ?>
                        <?php } ?>
                    </div>
                <?php } ?>
                <div class="info">
                    <div class="title">
                        <?php if ($url) { ?>
                            <a href="<?php echo $url; ?>"><?php html($item['title']); ?></a>
                        <?php } else { ?>
                            <?php html($item['title']); ?>
                        <?php } ?>
                        <?php if ($item['is_private']) { ?>
                            <span class="is_private" title="<?php html(LANG_PRIVACY_HINT); ?>"></span>
                        <?php } ?>
                    </div>
                </div>
            </div>

        <?php } ?>
    </div>

<?php } ?>