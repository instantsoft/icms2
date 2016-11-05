<ul class="links adb_list">
    <?php foreach ($items as $item) { ?>
        <li>
            <a href="<?php echo href_to($item['target_url']) . "#comment_{$item['id']}"; ?>"><?php echo html_strip($item['target_title'], 50); ?></a>
            <div><?php echo html_clean($item['content_html'], 100); ?></div>
            <div class="date"><?php echo string_date_age_max($item['date_pub'], true); ?></div>
        </li>
    <?php } ?>
</ul>
<?php if($show_count < $total){ ?>
<a class="view_all_link" href="<?php echo href_to('admin', 'controllers', array('edit', 'comments', 'comments_list')); ?>?filter=is_approved=0"><?php echo LANG_COMMENTS_ALL_LIST; ?></a>
<?php } ?>
