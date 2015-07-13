<?php // Шаблон списка уведомлений // ?>

<?php if (!$notices) { ?>
    <div class="notice"><?php echo LANG_PM_NO_NOTICES; ?></div>
    <?php return; ?>
<?php } ?>

<div id="pm_notices_window" data-action-url="<?php echo $this->href_to('notice_action'); ?>">

    <div id="pm_notices_list">

        <?php foreach($notices as $notice){ ?>

            <div id="notice-<?php echo $notice['id']; ?>" class="item">

                <?php if ($notice['options']['is_closeable']){ ?>
                    <div class="close-button"><a href="#close" onclick="icms.messages.noticeAction(<?php echo $notice['id']; ?>, 'close')" title="<?php echo LANG_CLOSE; ?>"></a></div>
                <?php } ?>

                <div class="date"><?php echo html_date_time($notice['date_pub']); ?></div>
                <div class="content"><?php echo $notice['content']; ?></div>

                <?php if ($notice['actions']){ ?>
                    <div class="buttons">
                        <?php foreach($notice['actions'] as $name=>$action){ ?>
                            <?php echo html_button($action['title'], $name, "icms.messages.noticeAction({$notice['id']}, '{$name}')", array('class'=>'button-small')); ?>
                        <?php } ?>
                    </div>
                <?php } ?>

            </div>

        <?php } ?>

    </div>

</div>
