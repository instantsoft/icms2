<?php if ($items){ ?>

    <div class="widget_content_list tiles-small">
        <?php foreach($items as $item) { ?>

            <?php
                $url        = href_to($ctype['name'], $item['slug'] . '.html');
                $is_private = $item['is_private'] && $hide_except_title && !$item['user']['is_friend'];
                $image      = (($image_field && !empty($item[$image_field])) ? $item[$image_field] : '');
                if ($is_private) {
                    if($image_field && !empty($item[$image_field])){
                        $image  = default_images('private', 'small');
                    }
                    $url = '';
                }
                if(!$image){ continue; }
            ?>

            <div class="item">
                <div class="image">
                    <?php if ($url) { ?>
                        <a href="<?php echo $url; ?>" title="<?php html($item['title']); ?>">
                            <?php echo html_image($image, 'small', $item['title']); ?>
                        </a>
                    <?php } else { ?>
                        <div title="<?php html($item['title']); ?>"><?php echo html_image($image, 'small', $item['title']); ?></div>
                    <?php } ?>
                </div>
            </div>

        <?php } ?>
    </div>

<?php } ?>