<?php if ($items){ ?>

    <div class="widget_content_list tiles-small">
        <?php foreach($items as $item) { ?>

            <?php $url = href_to($ctype['name'], $item['slug']) . '.html'; ?>

            <div class="item">
                <?php if ($image_field && !empty($item[$image_field])) { ?>
                    <div class="image">
                        <a href="<?php echo $url; ?>" title="<?php html($item['title']); ?>"><?php echo html_image($item[$image_field]); ?></a>
                    </div>
                <?php } ?>
            </div>

        <?php } ?>
    </div>

<?php } ?>