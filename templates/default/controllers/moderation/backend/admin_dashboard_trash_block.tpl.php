<ul class="links adb_list">
    <?php foreach ($logs as $log) { ?>
        <li>
            <a href="<?php echo href_to($log['target_subject'], $log['data']['slug'].'.html'); ?>"><?php echo html_strip($log['data']['title'], 50); ?></a>
            <div><?php echo $log['subject_title']; ?></div>
            <div class="date"><?php echo sprintf(LANG_MODERATION_CLEAR_THROUGH, string_date_age_max($log['date_expired'])); ?></div>
        </li>
    <?php } ?>
</ul>
<?php if($show_count < $total){ ?>
    <a class="view_all_link" href="<?php echo href_to('admin', 'controllers', array('edit', 'moderation', 'logs')); ?>?action=0&only_to_delete=1"><?php echo LANG_MODERATION_ALL_LIST; ?></a>
<?php } ?>