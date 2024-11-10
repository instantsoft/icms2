<div id="pm_notices_window" data-action-url="<?php echo href_to('messages', 'notice_action'); ?>">
    <div class="dropdown-header text-center">
        <strong><?php echo LANG_ADMIN_NOTICES; ?></strong>
    </div>
<?php if (!$notices) { ?>
    <div class="dropdown-item text-center text-muted"><?php echo LANG_PM_NO_NOTICES; ?></div>
    <?php return; ?>
<?php } ?>

<?php foreach($notices as $notice){ ?>
    <div id="notice-<?php echo $notice['id']; ?>" data-id="<?php echo $notice['id']; ?>" class="item p-3 border-bottom<?php if ($notice['actions']){ ?> has_actions<?php } ?>">
        <?php if ($notice['options']['is_closeable']){ ?>
            <button title="<?php echo LANG_CLOSE; ?>" type="button" class="close">
                <span aria-hidden="true">&times;</span>
            </button>
        <?php } ?>
        <div class="date small text-muted">
            <?php echo html_date_time($notice['date_pub']); ?>
        </div>
        <div class="content mt-2">
            <?php echo $notice['content']; ?>
        </div>
        <?php if ($notice['actions']){ ?>
            <div class="d-flex mt-3 mr-n2">
                <?php foreach($notice['actions'] as $name => $action){ ?>
                    <?php echo html_button($action['title'], $name, '', ['class'=>'btn-sm btn-light mr-2 btn-action']); ?>
                <?php } ?>
            </div>
        <?php } ?>
    </div>
<?php } ?>

<?php if(count($notices) > 2){ ?>
    <a class="dropdown-item text-center" id="clear-all-notices" href="#">
        <strong><?php echo LANG_PM_CLEAR_NOTICE; ?></strong>
    </a>
<?php } ?>
</div>
<script nonce="<?php echo $this->nonce; ?>">
    <?php echo $this->getLangJS('LANG_YES', 'LANG_NO', 'LANG_PM_CLEAR_NOTICE_CONFIRM');?>
    var pm_notices_window = $('#pm_notices_window');
    $('.close', pm_notices_window).on('click', function(){
        return icms.notices.noticeAction($(this).closest('.item').data('id'), 'close');
    });
    $('#clear-all-notices', pm_notices_window).on('click', function(){
        return icms.notices.noticeClear();
    });
    $('.btn-action', pm_notices_window).on('click', function(){
        return icms.notices.noticeAction($(this).closest('.item').data('id'), $(this).attr('name'));
    });
</script>