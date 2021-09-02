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

        <h3><?php echo $date; ?></h3>
        <?php $last_date = $msg_date; ?>

    <?php } ?>

    <div id="message-<?php echo $message['id']; ?>" class="message <?php if($message['user']['id']==$user->id){ ?>message-my<?php } ?>">
        <div class="user_avatar"><?php echo html_avatar_image($message['user']['avatar'], 'micro'); ?></div>
        <div class="content <?php if($message['user']['id']==$user->id){ ?>is_can_select<?php } ?>" data-id="<?php echo $message['id']; ?>">
            <div class="title">
                <span class="author"><?php echo $message['user']['nickname']; ?></span>
                <span class="date<?php if($message['is_new']){ ?>-new<?php } ?>"><?php echo ($is_today ? html_time($message['date_pub']): html_date_time($message['date_pub'])); ?></span>
            </div>
            <div class="message_text"><?php echo $message['content']; ?></div>
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
