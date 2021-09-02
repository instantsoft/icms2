<div class="dropdown-header text-center" id="pm_notices_window" data-action-url="<?php echo href_to('messages', 'notice_action'); ?>">
    <strong><?php echo LANG_ADMIN_NOTICES; ?></strong>
</div>
<?php if (!$notices) { ?>
    <div class="dropdown-item text-center text-muted"><?php echo LANG_PM_NO_NOTICES; ?></div>
    <?php return; ?>
<?php } ?>

<?php foreach($notices as $notice){ ?>
    <div id="notice-<?php echo $notice['id']; ?>" class="item p-3 border-bottom<?php if ($notice['actions']){ ?> has_actions<?php } ?>">
        <?php if ($notice['options']['is_closeable']){ ?>
            <button title="<?php echo LANG_CLOSE; ?>" type="button" class="close" aria-label="<?php echo LANG_CLOSE; ?>" onclick="return icms.notices.noticeAction(<?php echo $notice['id']; ?>, 'close')">
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
            <div class="buttons mt-2">
                <?php foreach($notice['actions'] as $name=>$action){ ?>
                    <?php echo html_button($action['title'], $name, "icms.notices.noticeAction({$notice['id']}, '{$name}')", array('class'=>'btn-sm btn-primary')); ?>
                <?php } ?>
            </div>
        <?php } ?>
    </div>
<?php } ?>

<script>
    <?php echo $this->getLangJS('LANG_YES', 'LANG_NO', 'LANG_PM_CLEAR_NOTICE_CONFIRM');?>
</script>

<?php if(count($notices) > 2){ ?>
    <a class="dropdown-item text-center" href="#" onclick="return icms.notices.noticeClear();">
        <strong><?php echo LANG_PM_CLEAR_NOTICE; ?></strong>
    </a>
<?php } ?>
