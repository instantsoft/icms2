<?php $this->addTplJSName('jquery.scrollbar'); ?>
<div id="icms_users_wrap">
    <div class="icms_users_wrap scrollbar-macosx">
        <ul class="links adb_list">
            <?php foreach ($profiles as $item) { ?>
                <li>
                    <a href="<?php echo href_to_profile($item); ?>"><?php echo html_strip($item['nickname'], 50); ?></a>
                    <div>
                        <?php echo LANG_REGISTRATION; ?> <?php echo string_date_age_max($item['date_reg'], true); ?>
                    </div>
                </li>
            <?php } ?>
        </ul>
    </div>
</div>
<script>
    $(function() {
        $('#icms_users_wrap .icms_users_wrap').scrollbar();
    });
</script>