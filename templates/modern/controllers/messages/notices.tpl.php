<?php if (!$notices) { ?>
    <div class="notice alert alert-info m-4"><?php echo LANG_PM_NO_NOTICES; ?></div>
    <?php return; ?>
<?php } ?>
<?php $this->addTplJSNameFromContext('messages'); ?>
<div id="pm_notices_window" data-action-url="<?php echo $this->href_to('notice_action'); ?>">

    <?php if(count($notices) > 2){ ?>
        <?php echo html_button(LANG_PM_CLEAR_NOTICE, 'clear_notice', "icms.messages.noticeClear()", ['class' => 'btn-primary btn-block mb-3']); ?>
    <?php } ?>

    <div id="pm_notices_list" class="mb-n3">

        <?php foreach($notices as $notice){ ?>

            <div id="notice-<?php echo $notice['id']; ?>" class="alert alert-secondary item<?php if ($notice['actions']){ ?> has_actions<?php } ?>">

                <?php if ($notice['options']['is_closeable']){ ?>
                    <button type="button" class="close" onclick="return icms.messages.noticeAction(<?php echo $notice['id']; ?>, 'close')" title="<?php echo LANG_CLOSE; ?>">
                        <span aria-hidden="true">&times;</span>
                    </button>
                <?php } ?>

                <div class="date text-muted"><?php echo html_date_time($notice['date_pub']); ?></div>
                <div class="content mt-1"><?php echo $notice['content']; ?></div>

                <?php if ($notice['actions']){ ?>
                    <div class="buttons mt-1">
                        <?php foreach($notice['actions'] as $name=>$action){ ?>
                            <?php echo html_button($action['title'], $name, "icms.messages.noticeAction({$notice['id']}, '{$name}')", ['class' => 'btn-secondary btn-sm']); ?>
                        <?php } ?>
                    </div>
                <?php } ?>

            </div>

        <?php } ?>

    </div>

</div>
<script>
    <?php echo $this->getLangJS('LANG_YES', 'LANG_NO', 'LANG_PM_CLEAR_NOTICE_CONFIRM');?>
</script>
