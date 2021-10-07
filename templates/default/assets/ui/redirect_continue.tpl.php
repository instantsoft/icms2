<div class="modal_padding">
    <?php
    $messages = cmsUser::getSessionMessages();
    if ($messages){ ?>
        <div class="sess_messages">
            <?php foreach($messages as $message){ ?>
                <div class="message_<?php echo $message['class']; ?>"><?php echo $message['text']; ?></div>
             <?php } ?>
        </div>
    <?php } ?>
    <form id="redirect_form" action="<?php html($redirect_url); ?>" method="post">
        <?php echo html_submit(LANG_CONTINUE); ?>
    </form>
</div>
<script>
    setTimeout(function (){
        $('#redirect_form .button-submit').trigger('click');
    }, 1000);
</script>