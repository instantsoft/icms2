<?php
    $today_date     = date('j F Y');
    $yesterday_date = date('j F Y', time() - 3600 * 24);
?>

<?php foreach($messages as $message){ ?>

    <?php $msg_date = date('j F Y', strtotime($message['date_pub'])); ?>
    <?php $is_today = $msg_date == $today_date; ?>

    <?php if ($msg_date != $last_date){ ?>

        <?php
            switch ($msg_date) {
                case $today_date: $date = LANG_TODAY;
                    break;
                case $yesterday_date: $date = LANG_YESTERDAY;
                    break;
                default: $date = lang_date($msg_date);
            }
        ?>

        <h6 class="text-secondary text-center my-3"><?php echo $date; ?></h6>
        <?php $last_date = $msg_date; ?>

    <?php } ?>

    <div id="message-<?php echo $message['id']; ?>" class="text-left mt-3 mb-2 message row no-gutters <?php if($message['user']['id']==$user->id){ ?> message-my flex-row-reverse<?php } ?>">
        <div class="col-auto">
            <div class="user_avatar icms-user-avatar mx-2 <?php if($message['user']['id']==$user->id){ ?>ml-3<?php } else { ?>mr-3<?php } ?>" data-id="<?php echo $message['id']; ?>">
                <?php if($message['user']['avatar']){ ?>
                    <?php echo html_avatar_image($message['user']['avatar'], 'micro', $message['user']['nickname']); ?>
                <?php } else { ?>
                    <?php echo html_avatar_image_empty($message['user']['nickname'], 'avatar__mini'); ?>
                <?php } ?>
            </div>
        </div>
        <div class="col flex-grow-1 ">
            <div class="content py-2 px-3 rounded <?php if($message['user']['id']==$user->id){ ?>is_can_select bg-white ml-2<?php } else { ?>mr-2 bg-success_light<?php } ?>" data-id="<?php echo $message['id']; ?>">
                <div class="title d-flex justify-content-between">
                    <b class="author">
                        <?php echo $message['user']['nickname']; ?>
                    </b>
                    <small class="date<?php if($message['is_new']){ ?>-new highlight_new<?php } ?>">
                        <?php html_svg_icon('solid', 'clock'); ?>
                        <?php echo ($is_today ? html_time($message['date_pub']): html_date_time($message['date_pub'])); ?>
                    </small>
                </div>
                <div class="message_text text-break">
                    <?php echo $message['content']; ?>
                </div>
            </div>
        </div>
    </div>

<?php } ?>
<script>
    icms.messages.setMsgLastDate('<?php echo $last_date; ?>');
</script>
<?php if(!empty($is_notify)) { ?>

<script>
    icms.messages.desktopNotification(
        "<?php html(sprintf(LANG_PM_DESKTOP_NOTIFY_NEW, $message['user']['nickname'])); ?>", {
            tag: "icms_msg<?php echo $message['user']['id']; ?>",
            body: "<?php html(html_clean($message['content'], 50)); ?>",
            icon: "<?php echo html_avatar_image_src($message['user']['avatar'], 'micro', false); ?>"
        }
    );
</script>

<?php }
