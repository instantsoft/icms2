<?php if ($items){ ?>

    <div class="widget_content_list compact">
        <?php foreach($items as $item) { ?>

            <?php $url = href_to($ctype['name'], $item['slug']) . '.html'; ?>

            <div class="item">
                <?php if ($image_field && !empty($item[$image_field])) { ?>
                    <div class="image">
                        <a href="<?php echo $url; ?>"><?php echo html_image($item[$image_field], 'micro'); ?></a>
                    </div>
                <?php } ?>
                <div class="info">
                    <div class="title">
                        <a href="<?php echo $url; ?>"><?php html($item['title']); ?></a>
                    </div>
                </div>
            </div>

        <?php } ?>
    </div>

<?php } ?>