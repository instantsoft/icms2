<?php if (!$notices) { ?>
    <div class="notice alert alert-info m-4"><?php echo LANG_PM_NO_NOTICES; ?></div>
    <?php return; ?>
<?php } ?>
<?php $this->addTplJSNameFromContext('messages'); ?>
<div id="pm_notices_window" data-action-url="<?php echo $this->href_to('notice_action'); ?>">

    <?php if(count($notices) > 2){ ?>
        <?php echo html_button(LANG_PM_CLEAR_NOTICE, '', '', ['class' => 'btn-primary btn-block mb-3 notices-clear']); ?>
    <?php } ?>

    <div id="pm_notices_list" class="mb-n3">

        <?php foreach($notices as $notice){ ?>

            <div id="notice-<?php echo $notice['id']; ?>" class="alert alert-secondary item<?php if ($notice['actions']){ ?> has_actions<?php } ?>">

                <?php if ($notice['options']['is_closeable']){ ?>
                    <button type="button" class="close" title="<?php echo LANG_CLOSE; ?>" data-id="<?php echo $notice['id']; ?>">
                        <span aria-hidden="true">&times;</span>
                    </button>
                <?php } ?>

                <div class="date text-muted"><?php echo html_date_time($notice['date_pub']); ?></div>
                <div class="content mt-1"><?php echo $notice['content']; ?></div>

                <?php if ($notice['actions']){ ?>
                    <div class="buttons mt-1">
                        <?php foreach($notice['actions'] as $name => $action){ ?>
                            <?php echo html_button($action['title'], '', '', ['class' => 'btn-secondary btn-sm notice-action', 'data-id' => $notice['id'], 'data-name' => $name]); ?>
                        <?php } ?>
                    </div>
                <?php } ?>

            </div>

        <?php } ?>

    </div>

</div>
<script>
    <?php echo $this->getLangJS('LANG_YES', 'LANG_NO', 'LANG_PM_CLEAR_NOTICE_CONFIRM');?>
    $('#pm_notices_window').on('click', '.close', function(){
        return icms.messages.noticeAction($(this).data('id'), 'close');
    }).on('click', '.notices-clear', function(){
        return icms.messages.noticeClear();
    }).on('click', '.notice-action', function(){
        return icms.messages.noticeAction($(this).data('id'), $(this).data('name'));
    });
</script>
