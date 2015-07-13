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
