<?php // Шаблон одного сообщения // ?>

<?php foreach($messages as $message){ ?>

    <div id="message-<?php echo $message['id']; ?>" class="message <?php if($message['user']['id']==$user->id){ ?>message-my<?php } ?>">
        <div class="title">
            <span class="author"><?php echo $message['user']['nickname']; ?></span>
            <span class="date<?php if($message['is_new']){ ?>-new<?php } ?>"><?php echo html_date_time($message['date_pub']); ?></span>
        </div>
        <div class="content"><?php echo $message['content']; ?></div>
    </div>

<?php } ?>

<?php if(!empty($is_notify)) { ?>

<script type="text/javascript">
    icms.messages.desktopNotification(
        "<?php html(sprintf(LANG_PM_DESKTOP_NOTIFY_NEW, $message['user']['nickname'])); ?>", {
            tag: "icms_msg<?php echo $message['user']['id']; ?>",
            body: "<?php html(html_clean($message['content'], 50)); ?>",
            icon: "<?php echo html_avatar_image_src($message['user']['avatar'], 'micro', false); ?>"
        }
    );
</script>

<?php } ?>