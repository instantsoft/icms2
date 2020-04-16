<?php $is_first = true; ?>
<?php foreach ($items as $item) { ?>
    <div class="flex-column align-items-start <?php if(!$is_first){ ?>mt-2<?php } ?>">
        <div class="d-flex w-100 justify-content-between">
            <h5 class="mb-1">
                <a href="<?php echo $item['url']; ?>"><?php echo html_strip($item['title'], 50); ?></a>
            </h5>
            <small class="text-muted">
                <?php echo string_date_age_max($item['date_pub'], true); ?>
            </small>
        </div>
        <div>
            <?php echo $item['ctype_title']; ?>
        </div>
    </div>
    <?php $is_first = false; ?>
<?php } ?>
<?php if($show_count < $total){ ?>
    <a class="mt-3 btn btn-secondary btn-block" href="<?php echo href_to('moderation'); ?>"><?php echo LANG_MODERATION_ALL_LIST; ?></a>
<?php } ?>