<ul class="links adb_list">
    <?php foreach ($items as $item) { ?>
        <li>
            <a href="<?php echo $item['url']; ?>"><?php echo html_strip($item['title'], 50); ?></a>
            <div><?php echo $item['ctype_title']; ?></div>
            <div class="date"><?php echo string_date_age_max($item['date_pub'], true); ?></div>
        </li>
    <?php } ?>
</ul>
<?php if($show_count < $total){ ?>
    <a class="view_all_link" href="<?php echo href_to('moderation'); ?>"><?php echo LANG_MODERATION_ALL_LIST; ?></a>
<?php } ?>