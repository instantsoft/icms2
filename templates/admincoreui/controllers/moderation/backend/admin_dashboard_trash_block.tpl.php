<?php $is_first = true; ?>
<?php foreach ($logs as $log) { ?>
    <div class="flex-column align-items-start <?php if(!$is_first){ ?>mt-2<?php } ?>">
        <div class="d-flex w-100 justify-content-between">
            <h5 class="mb-1">
                <a href="<?php echo href_to($log['target_subject'], $log['data']['slug'].'.html'); ?>"><?php echo html_strip($log['data']['title'], 50); ?></a>
            </h5>
            <small class="text-muted">
                <?php echo sprintf(LANG_MODERATION_CLEAR_THROUGH, string_date_age_max($log['date_expired'])); ?>
            </small>
        </div>
        <div>
            <?php echo $log['subject_title']; ?>
        </div>
    </div>
    <?php $is_first = false; ?>
<?php } ?>
<?php if($show_count < $total){ ?>
    <a class="mt-3 btn btn-secondary btn-block" href="<?php echo href_to('admin', 'controllers', array('edit', 'moderation', 'logs')); ?>?action=0&only_to_delete=1"><?php echo LANG_MODERATION_ALL_LIST; ?></a>
<?php } ?>